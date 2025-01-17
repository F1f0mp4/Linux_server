To start the script and ensure it runs continuously (including processing existing and new `.mkv` files), you can follow these steps:

### Step 1: Save the Script

Save the updated script to a file. For example, save it as `convert_mkv_to_mp4.sh` in your home directory or another preferred location:

```bash
nano ~/convert_mkv_to_mp4.sh
```

```bash
#!/bin/bash

# Directory to monitor
WATCH_DIR="/mnt/smbshare/MOVIES"

# Process existing .mkv files first
for input_file in "$WATCH_DIR"/*.mkv; do
    if [[ -f "$input_file" ]]; then
        output_file="${input_file%.mkv}.mp4"
        
        # Convert .mkv to .mp4 using ffmpeg
        echo "Converting $input_file to $output_file"
        ffmpeg -i "$input_file" -c copy "$output_file"

        # Check if conversion was successful, then delete the .mkv
        if [[ $? -eq 0 ]]; then
            echo "Conversion successful. Deleting original .mkv file."
            rm "$input_file"
        else
            echo "Conversion failed."
        fi
    fi
done

# Monitor for new .mkv files
inotifywait -m -e create --format "%f" "$WATCH_DIR" | while read filename
do
    # Check if the file is an .mkv
    if [[ "$filename" == *.mkv ]]; then
        echo "Detected new .mkv file: $filename"
        
        # Define input and output file paths
        input_file="$WATCH_DIR/$filename"
        output_file="${input_file%.mkv}.mp4"

        # Convert .mkv to .mp4 using ffmpeg
        echo "Converting $input_file to $output_file"
        ffmpeg -i "$input_file" -c copy "$output_file"

        # Check if conversion was successful, then delete the .mkv
        if [[ $? -eq 0 ]]; then
            echo "Conversion successful. Deleting original .mkv file."
            rm "$input_file"
        else
            echo "Conversion failed."
        fi
    fi
done
```

Paste the script content into the file and save it (press `Ctrl + O` to save, then `Ctrl + X` to exit).

### Step 2: Make the Script Executable

Make the script executable so that it can be run:

```bash
chmod +x ~/convert_mkv_to_mp4.sh
```

### Step 3: Run the Script Manually (For Testing)

You can run the script manually to test it and verify that it works:

```bash
~/convert_mkv_to_mp4.sh
```

This will process both existing `.mkv` files and monitor for new ones.

### Step 4: Run the Script as a Background Process (Optional)

If you want the script to run continuously in the background, you can use `nohup` to keep it running even if the terminal is closed:

```bash
nohup ~/convert_mkv_to_mp4.sh &
```

This will start the script in the background and redirect its output to a file called `nohup.out`. You can check the output by running:

```bash
tail -f nohup.out
```

### Step 5: Set Up the Script to Run Automatically on System Boot (Optional)

If you want the script to start automatically when the system boots, you can set it up as a `systemd` service, as explained in the previous response. Here's a quick recap:

1. Create a `systemd` service file:

   ```bash
   sudo nano /etc/systemd/system/mkv-to-mp4.service
   ```

2. Add the following content to the service file:

   ```ini
   [Unit]
   Description=Automatic MKV to MP4 Conversion
   After=network.target

   [Service]
   ExecStart=/home/your-username/convert_mkv_to_mp4.sh
   Restart=always
   User=your-username

   [Install]
   WantedBy=multi-user.target
   ```

   Replace `/home/your-username/convert_mkv_to_mp4.sh` with the correct path to the script and `your-username` with your actual username.

3. Enable and start the service:

   ```bash
   sudo systemctl enable mkv-to-mp4.service
   sudo systemctl start mkv-to-mp4.service
   ```

This will ensure that the script runs automatically every time your system starts, and it will continue processing `.mkv` files in the `/mnt/smbshare/MOVIES` directory.

Let me know if you encounter any issues or need further assistance!
