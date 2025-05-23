!/bin/bash

# === CONFIGURATION ===
SERVER_DIR="/mnt/minecraft/Middle-Earth_1.21"
BACKUP_DIR="/mnt/nextcloud/smbshare/shifty/minecraft_backup"
MAX_BACKUPS=7
SCREEN_NAME="minecraft"

# === FUNCTION TO CHECK ONLINE PLAYERS ===
players_online() {
    screen -S "$SCREEN_NAME" -p 0 -X stuff "list\n"
    sleep 1
    output=$(tail -n 20 "$SERVER_DIR/logs/latest.log" | grep "players online")

    if echo "$output" | grep -q "[1-9][0-9]* players online"; then
        return 0
    else
        return 1
    fi
}

# === FUNCTION TO SEND CHAT MESSAGE ===
send_chat_message() {
    local msg="$1"
    # Append carriage return for command execution in screen
    screen -S "$SCREEN_NAME" -p 0 -X stuff "say $msg$(printf \\r)"
}

# === FUNCTION TO PERFORM BACKUP ===
perform_backup() {
    TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
    BACKUP_NAME="world_backup_$TIMESTAMP.tar.gz"
    TMP_BACKUP="/tmp/$BACKUP_NAME"

    echo "[INFO] Backing up Minecraft world..."

    INCLUDE_DIRS=("world")
    if [ -d "$SERVER_DIR/mods" ]; then
        echo "[INFO] Mods folder detected — including in backup."
        INCLUDE_DIRS+=("mods")
    else
        echo "[INFO] No mods folder found — skipping."
    fi

    tar -czf "$TMP_BACKUP" -C "$SERVER_DIR" "${INCLUDE_DIRS[@]}"

    echo "[INFO] Moving backup to target directory..."
    mv "$TMP_BACKUP" "$BACKUP_DIR"

    echo "[INFO] Cleaning up old backups..."
    ls -1t "$BACKUP_DIR"/world_backup_*.tar.gz | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm --

    echo "[INFO] Backup complete: $BACKUP_NAME"
}

# === FUNCTION TO ANNOUNCE BACKUP COMPLETION ===
send_backup_complete_message() {
    send_chat_message "Backup completed successfully!"
}

# === MAIN SCRIPT EXECUTION ===
cd "$SERVER_DIR" || { echo "[ERROR] Cannot change directory to $SERVER_DIR"; exit 1; }

if players_online; then
    echo "[INFO] Players online detected. Sending chat notification."
    send_chat_message "World is backing up. Expect minor lag."
else
    echo "[INFO] No players online. Proceeding silently."
fi

perform_backup

send_backup_complete_message
