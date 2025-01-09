# Notes: Video Conversion and Upscaling on Linux

## 1. Converting MKV to MP4 on Linux

You can use `ffmpeg`, a powerful multimedia processing tool, to convert MKV files to MP4.

### Install `ffmpeg`:
```bash
sudo apt update
sudo apt install ffmpeg
```

### Convert MKV to MP4 Without Re-encoding:
```bash
ffmpeg -i input.mkv -c copy output.mp4
```
- `-i input.mkv`: Specifies the input file.
- `-c copy`: Copies the video and audio streams without re-encoding (faster process).
- `output.mp4`: Desired output file.

### Convert MKV to MP4 With Re-encoding:
```bash
ffmpeg -i input.mkv -vcodec libx264 -acodec aac output.mp4
```
- `-vcodec libx264`: Encodes the video stream with H.264 codec.
- `-acodec aac`: Encodes the audio stream with AAC codec.

### Handling File Names with Spaces:
If the file name contains spaces or special characters, use one of these methods:

#### Using Escaped Spaces:
```bash
ffmpeg -i Harry\ Potter\ \(7-1\)\ A\ Dary\ smrti\ 1.mkv -c copy Harry\ Potter\ \(7-1\)\ A\ Dary\ smrti\ 1.mp4
```

#### Using Quotes:
```bash
ffmpeg -i "Harry Potter (7-1) A Dary smrti 1.mkv" -c copy "Harry Potter (7-1) A Dary smrti 1.mp4"
```

## 2. Upscaling Video from 1080p to 4K
It is possible to upscale a video from 1080p to 4K, but note that this won’t add new detail—it only increases the resolution, which may lead to loss of sharpness or artifacts.

### Upscale to 4K Using `ffmpeg`:
```bash
ffmpeg -i input.mkv -vf "scale=3840:2160" -c:v libx264 -c:a copy output_4k.mp4
```
- `-vf "scale=3840:2160"`: Applies the scaling filter to upscale the video to 4K resolution (3840x2160).
- `-c:v libx264`: Specifies the video codec (H.264).
- `-c:a copy`: Copies the audio stream without re-encoding.

### Key Points:
- **Installation**: Make sure `ffmpeg` is installed on your system.
- **Container Conversion**: Use `-c copy` for fast container changes without re-encoding.
- **Re-encoding**: Required for codec changes or specific compatibility.
- **Upscaling**: Use scaling filters to change resolution, though quality may not improve significantly.

---