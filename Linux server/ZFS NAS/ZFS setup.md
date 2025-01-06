# Setting Up a Home NAS with ZFS on Ubuntu (2x 6TB Disks)

This guide will help you configure a **home NAS** using **ZFS** on Ubuntu, with built-in compression and redundancy for storing videos, photos, and documents. It assumes you have two 6TB drives and plans to add another in the future.

---

## Prerequisites

- **Ubuntu** installed and running.
- Two 6TB disks ready for setup (identified as `/dev/sdX` and `/dev/sdY`).
- Basic familiarity with terminal commands.

---

## Step 1: Install ZFS

Update your system and install the ZFS utilities:

```bash
sudo apt update
sudo apt install zfsutils-linux
```

Verify the installation:

```bash
zfs version
```

---

## Step 2: Create a ZFS Pool

### Identify Your Disks
Use `lsblk` to list all storage devices and identify your two 6TB disks:

```bash
lsblk
```

### Create a ZFS Pool in Mirror Mode
To ensure redundancy, configure your two disks in a mirrored pool:

```bash
sudo zpool create naspool mirror /dev/sdX /dev/sdY
```
Replace `/dev/sdX` and `/dev/sdY` with your actual disk identifiers.

Verify the pool:

```bash
sudo zpool status
```

---

## Step 3: Enable Compression

Enable ZFS compression using the **Zstd** algorithm for a balance of speed and efficiency:

```bash
sudo zfs set compression=zstd naspool
```

Confirm compression is enabled:

```bash
sudo zfs get compression naspool
```

---

## Step 4: Create Datasets for Users

Create separate datasets for family members or types of data:

```bash
sudo zfs create naspool/mom
sudo zfs create naspool/dad
sudo zfs create naspool/sister
sudo zfs create naspool/me
```

Set permissions for each dataset:

```bash
sudo chown -R mom:group /naspool/mom
sudo chown -R dad:group /naspool/dad
...
```

---

## Step 5: Mount the Pool

By default, ZFS pools are mounted under `/naspool`. To change this:

```bash
sudo zfs set mountpoint=/mnt/naspool naspool
```

---

## Step 6: Access Your NAS Remotely

### Option 1: Samba (SMB)
Install Samba for network file sharing:

```bash
sudo apt install samba
```

Configure Samba to share the ZFS datasets:

```bash
sudo nano /etc/samba/smb.conf
```
Add:

```ini
[naspool]
   path = /mnt/naspool
   browseable = yes
   read only = no
   guest ok = yes
```

Restart Samba:

```bash
sudo systemctl restart smbd
```

### Option 2: Remote Access via SSH
Ensure SSH is enabled for secure remote access:

```bash
sudo apt install openssh-server
sudo systemctl enable ssh
sudo systemctl start ssh
```

---

## Step 7: Expand the Pool in the Future

When adding another 6TB drive:

1. Add it as a new **vdev** for more capacity:
   ```bash
   sudo zpool add naspool /dev/sdZ
   ```
   Replace `/dev/sdZ` with the new disk identifier.

2. Alternatively, convert to a **RAID-Z** configuration for more redundancy (requires a full rebuild).

---

## Monitoring and Maintenance

### Check Pool Status
Regularly check the health of your pool:

```bash
sudo zpool status
```

### Scrub the Pool
Run periodic scrubs to detect and repair data corruption:

```bash
sudo zpool scrub naspool
```

---

You now have a robust, redundant, and efficient NAS setup with ZFS on Ubuntu! This configuration is optimized for home use, ensuring your data is secure and accessible.

