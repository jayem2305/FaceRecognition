import sys
from facenet_pytorch import MTCNN, InceptionResnetV1
from PIL import Image
import torch
import json

def recognize_face(image_path):
    try:
        # Load image and convert to RGB format
        img = Image.open(image_path).convert('RGB')

        # Load MTCNN model for face detection
        mtcnn = MTCNN()

        # Detect face
        boxes, _ = mtcnn.detect(img)

        if boxes is not None:
            # Load InceptionResnetV1 model for face recognition
            resnet = InceptionResnetV1(pretrained='vggface2').eval()

            # Extract face embeddings
            img_cropped = mtcnn(img)
            embeddings = resnet(img_cropped.unsqueeze(0))

            return {
                'status': 'success',
                'data': embeddings.tolist()
            }
        else:
            return {
                'status': 'error',
                'message': 'No face detected'
            }

    except Exception as e:
        return {
            'status': 'error',
            'message': str(e)
        }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({'status': 'error', 'message': 'Missing image path argument'}))
        sys.exit(1)

    image_path = sys.argv[1]
    result = recognize_face(image_path)
    print(json.dumps(result))
