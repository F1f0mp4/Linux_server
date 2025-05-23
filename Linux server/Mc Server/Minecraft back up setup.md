## Step 1: Make sure your backup script is executable

Run this command once to ensure execution permissions:

```bash
chmod +x /mnt/minecraft/scripts/minecraft_backup.sh
```

---

## Step 2: Open your user’s crontab file for editing

```bash
crontab -e
```

If it’s your first time, you may be prompted to choose an editor — choose your preference (nano is easiest).

---

## Step 3: Add the cron job line

At the bottom of the opened file, add this exact line:

```cron
0 12 * * 4 /mnt/minecraft/scripts/minecraft_backup.sh >> /mnt/minecraft/scripts/backup.log 2>&1
```

* This means **run at 12:00 PM every Thursday**.
* Output (including errors) will be saved to `backup.log` in your scripts directory for troubleshooting or auditing.

---

## Step 4: Save and exit the editor

* If using nano: press `Ctrl + O` to save, then `Enter` to confirm, and `Ctrl + X` to exit.
* If using vim: press `Esc`, then type `:wq` and hit `Enter`.

---

## Step 5: Verify your cron job was added

Run:

```bash
crontab -l
```

You should see the line you just added:

```
0 12 * * 4 /mnt/minecraft/scripts/minecraft_backup.sh >> /mnt/minecraft/scripts/backup.log 2>&1
```

---

## Step 6: Confirm your server timezone

Make sure your server time matches your expectation for “12 PM”:

```bash
date
```

Adjust the schedule accordingly if your server is in a different timezone.

---

## Done.

Your Minecraft backup will now execute every Thursday at noon — hands-off, reliable, and auditable.

---

If you want to add monitoring, alerts, or move to systemd timers for next-level uptime and control, just say the word!
