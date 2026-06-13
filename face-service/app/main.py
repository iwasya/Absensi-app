from __future__ import annotations

import os
import tempfile
from dataclasses import dataclass
from io import BytesIO
from pathlib import Path

from deepface import DeepFace
from fastapi import FastAPI, File, Form, HTTPException, UploadFile
from PIL import Image

app = FastAPI(title="Absensi Face Verification Service")

MAX_IMAGE_BYTES = int(os.getenv("MAX_IMAGE_BYTES", str(5 * 1024 * 1024)))
MODEL_NAME = os.getenv("FACE_MODEL_NAME", "Facenet512")
DETECTOR_BACKENDS = [
    detector.strip()
    for detector in os.getenv(
        "FACE_DETECTOR_BACKENDS",
        os.getenv("FACE_DETECTOR_BACKEND", "mtcnn,opencv"),
    ).split(",")
    if detector.strip()
]


@dataclass(frozen=True)
class VerificationResult:
    payload: dict[str, object]
    detector: str


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok"}


@app.post("/verify-face")
async def verify_face(
    reference_image: UploadFile = File(...),
    candidate_image: UploadFile = File(...),
    threshold: float = Form(0.75),
    user_id: str | None = Form(None),
) -> dict[str, object]:
    reference_bytes = await _read_image(reference_image)
    candidate_bytes = await _read_image(candidate_image)

    _validate_image(reference_bytes, "reference_image")
    _validate_image(candidate_bytes, "candidate_image")

    reference_path = _write_temp_image(reference_bytes, reference_image.filename)
    candidate_path = _write_temp_image(candidate_bytes, candidate_image.filename)

    try:
        verification = _verify_with_fallbacks(reference_path, candidate_path, threshold)
    except FaceNotDetected as exc:
        return {
            "match": False,
            "confidence": 0.0,
            "message": "Wajah tidak terdeteksi pada foto profil atau foto absen.",
            "detail": str(exc),
            "user_id": user_id,
        }
    except Exception as exc:
        raise HTTPException(status_code=500, detail=f"Face verification failed: {exc}") from exc
    finally:
        reference_path.unlink(missing_ok=True)
        candidate_path.unlink(missing_ok=True)

    distance = float(verification.payload.get("distance", 1.0))
    model_threshold = float(verification.payload.get("threshold", threshold))
    match = bool(verification.payload.get("verified", False))
    confidence = _confidence_from_distance(distance, model_threshold)

    return {
        "match": match,
        "confidence": confidence,
        "distance": distance,
        "threshold": model_threshold,
        "model": MODEL_NAME,
        "detector": verification.detector,
        "user_id": user_id,
    }


class FaceNotDetected(ValueError):
    pass


def _verify_with_fallbacks(reference_path: Path, candidate_path: Path, threshold: float) -> VerificationResult:
    errors: list[str] = []

    for detector in DETECTOR_BACKENDS:
        try:
            result = DeepFace.verify(
                img1_path=str(reference_path),
                img2_path=str(candidate_path),
                model_name=MODEL_NAME,
                detector_backend=detector,
                enforce_detection=True,
                threshold=threshold,
                silent=True,
            )

            return VerificationResult(payload=result, detector=detector)
        except ValueError as exc:
            errors.append(f"{detector}: {exc}")

    detail = " | ".join(errors) if errors else "Tidak ada detector yang dikonfigurasi."
    raise FaceNotDetected(detail)


async def _read_image(upload: UploadFile) -> bytes:
    data = await upload.read()
    if len(data) > MAX_IMAGE_BYTES:
        raise HTTPException(status_code=413, detail=f"{upload.filename or 'image'} is too large")

    return data


def _validate_image(data: bytes, field_name: str) -> None:
    try:
        with Image.open(BytesIO(data)) as img:
            img.verify()
    except Exception as exc:
        raise HTTPException(status_code=422, detail=f"{field_name} is not a valid image") from exc


def _write_temp_image(data: bytes, filename: str | None) -> Path:
    suffix = Path(filename or "image.jpg").suffix.lower()
    if suffix not in {".jpg", ".jpeg", ".png", ".webp"}:
        suffix = ".jpg"

    fd, path = tempfile.mkstemp(suffix=suffix)
    with os.fdopen(fd, "wb") as file:
        file.write(data)

    return Path(path)


def _confidence_from_distance(distance: float, threshold: float) -> float:
    if threshold <= 0:
        return 0.0

    confidence = 1 - (distance / threshold)
    return max(0.0, min(1.0, confidence))
