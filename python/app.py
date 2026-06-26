from flask import Flask, request, jsonify, send_from_directory
import os
import cv2
import numpy as np
from werkzeug.utils import secure_filename
import uuid
import threading
import json
from typing import List, Dict

app = Flask(__name__)
UPLOAD_FOLDER = '/app/uploads'
RESULT_FOLDER = '/app/results'

# Create directories
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(RESULT_FOLDER, exist_ok=True)

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in {'png', 'jpg', 'jpeg'}

def extract_colors(image_path: str, n_colors: int=5):
    # IMREAD_UNCHANGED reads RGBA instead of BGR
    image = cv2.imread(image_path, cv2.IMREAD_UNCHANGED)
    if image is None:
        raise ValueError(f"Image not read: {image_path}")

    # Scale image (max. 1000px)
    height, width = image.shape[:2]
    if max(height, width) > 1000:
        scale = 1000 / max(height, width)
        image = cv2.resize(image, (int(width * scale), int(height * scale)))

    # Process alpha channel when available
    if image.shape[2] == 4:
        # Alpha mask: only pixel with alpha > 128
        alpha = image[:, :, 3]
        mask = alpha > 128

        # BGR → RGB only for opaque pixel
        rgb = cv2.cvtColor(image[:, :, :3], cv2.COLOR_BGR2RGB)
        pixels = rgb[mask].astype(np.float32)
    else:
        # No alpha channel
        rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        pixels = image.reshape((-1, 3)).astype(np.float32)

    if len(pixels) == 0:
        raise ValueError("No opaque pixels found after masking")

    # K-Means for color extraction
    criteria = (cv2.TERM_CRITERIA_EPS + cv2.TERM_CRITERIA_MAX_ITER, 200, 0.1)
    _, labels, centers = cv2.kmeans(
        pixels,
        n_colors,
        None,
        criteria,
        10,
        cv2.KMEANS_PP_CENTERS
    )
    palette = centers.astype(int)

    # Weight: Share of total pixel in cluster
    total_pixels = len(labels)
    weights = []
    for i in range(n_colors):
        count = np.sum(labels == i)
        percentage = round((count / total_pixels) * 100, 2)
        weights.append(percentage)

    # Return Hex-Codes + weight, sorted by weight DESC
    colors = [
        {
            "hex": f"#{palette[i][0]:02x}{palette[i][1]:02x}{palette[i][2]:02x}",
            "weight": weights[i]
        }
        for i in range(n_colors)
    ]
    colors.sort(key=lambda x: x["weight"], reverse=True)

    return colors

def process_batch(image_paths: List[str], n_colors: int = 5) -> Dict:
    """Processes multiple images asynchronously."""
    results = []
    for image_path in image_paths:
        try:
            colors = extract_colors(image_path, n_colors, "kmeans")
            results.append({
                "image": os.path.basename(image_path),
                "colors": colors,
                "status": "success"
            })
        except Exception as e:
            results.append({
                "image": os.path.basename(image_path),
                "error": str(e),
                "status": "error"
            })
    return {"results": results}

@app.route('/extract', methods=['POST'])
def extract_single():
    """Upload single image and extract colors."""
    if 'file' not in request.files:
        return jsonify({"error": "No image uploaded"}), 400

    file = request.files['file']
    if file.filename == '' or not allowed_file(file.filename):
        return jsonify({"error": "Only JPG/PNG allowed"}), 400

    filename = f"{uuid.uuid4()}_{secure_filename(file.filename)}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    file.save(filepath)

    try:
        n_colors = int(request.form.get('n_colors', 5))
        colors = extract_colors(filepath, n_colors)
        os.remove(filepath)  # Delete image
        return jsonify({"colors": colors})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/batch', methods=['POST'])
def batch_upload():
    """Batch-Upload: Process multiple images at once."""
    if 'files' not in request.files:
        return jsonify({"error": "No images uploaded"}), 400

    files = request.files.getlist('files')
    if not files or not all(allowed_file(f.filename) for f in files):
        return jsonify({"error": "Files not valid"}), 400

    # Save files with unique name
    image_paths = []
    for file in files:
        filename = f"{uuid.uuid4()}_{secure_filename(file.filename)}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        file.save(filepath)
        image_paths.append(filepath)

    # 👈 Asynchronous processing: Start thread and return Job-ID immediately
    job_id = str(uuid.uuid4())
    result_file = os.path.join(RESULT_FOLDER, f"{job_id}.json")

    def async_task():
        results = process_batch(image_paths)
        with open(result_file, 'w') as f:
            json.dump(results, f)
        # Delete images (optional)
        for path in image_paths:
            os.remove(path)

    threading.Thread(target=async_task).start()

    return jsonify({
        "job_id": job_id,
        "status": "processing",
        "message": "Batch-Processing started. Retrieve results later."
    })

@app.route('/batch/<job_id>', methods=['GET'])
def get_batch_results(job_id):
    """Retrieve Batch-Job results."""
    result_file = os.path.join(RESULT_FOLDER, f"{job_id}.json")
    if not os.path.exists(result_file):
        return jsonify({"error": "Job not found or not yet finished."}), 404

    with open(result_file, 'r') as f:
        results = json.load(f)
    os.remove(result_file)  # Delete results (optional)
    return jsonify(results)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, threaded=True)
