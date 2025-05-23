#!/bin/bash

# === CONFIGURATION ===
SERVER_DIR="/mnt/minecraft/"
BACKUP_DIR="/mnt/.../minecraft_backup"
MAX_BACKUPS=7
SCREEN_NAME="minecraft"

# === FUNCTION TO CHECK ONLINE PLAYERS ===
players_online() {
    screen -S "$SCREEN_NAME" -p 0 -X stuff "list$(printf \\r)"
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
    screen -S "$SCREEN_NAME" -p 0 -X stuff "say $msg$(printf \\r)"
}

# === FUNCTION TO PERFORM BACKUP ===
perform_backup() {
    TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
    BACKUP_NAME="full_backup_$TIMESTAMP.tar.gz"
    TMP_BACKUP="/tmp/$BACKUP_NAME"

    echo "[INFO] Starting backup: $BACKUP_NAME"

    # Collect critical components
    INCLUDE_ITEMS=(
        "world"
        "mods"
        "config"
        "server.properties"
        "eula.txt"
        "whitelist.json"
        "banned-players.json"
        "banned-ips.json"
        "ops.json"
        "usercache.json"
        "plugins"
    )

    echo "[INFO] Archiving important directories and configs..."

    tar -czf "$TMP_BACKUP" -C "$SERVER_DIR" "${INCLUDE_ITEMS[@]}" 2>/dev/null

    echo "[INFO] Moving archive to: $BACKUP_DIR"
    mv "$TMP_BACKUP" "$BACKUP_DIR"

    echo "[INFO] Enforcing backup retention: keeping last $MAX_BACKUPS"
    ls -1t "$BACKUP_DIR"/full_backup_*.tar.gz | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm --

    echo "[INFO] Backup complete: $BACKUP_NAME"
}

# === FUNCTION TO ANNOUNCE BACKUP COMPLETION ===
send_backup_complete_message() {
    send_chat_message "✅ Backup completed successfully!"
}

# === MAIN SCRIPT EXECUTION ===
cd "$SERVER_DIR" || { echo "[ERROR] Cannot change directory to $SERVER_DIR"; exit 1; }

if players_online; then
    echo "[INFO] Players online. Broadcasting backup start..."
    send_chat_message "⚠️ World is backing up. Expect minor lag..."
else
    echo "[INFO] No players online. Proceeding with silent backup."
fi

perform_backup

send_backup_complete_message
