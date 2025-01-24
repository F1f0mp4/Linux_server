

Here's a refined GitHub-style README for your Samba setup:

---

# Samba (SMB) File Sharing Setup on Ubuntu

A step-by-step guide to set up a secure and user-specific Samba (SMB) file share on Ubuntu.

---

## Table of Contents
1. [Partition and Format a Disk](#1-partition-and-format-a-disk)
2. [Mount the Disk](#2-mount-the-disk)
3. [Install and Configure Samba](#3-install-and-configure-samba)
4. [Create User-Specific Shares](#4-create-user-specific-shares)
5. [Shared Directory for All Users](#5-shared-directory-for-all-users)
6. [Testing the Configuration](#6-testing-the-configuration)

---

## 1. Partition and Format a Disk

Prepare `/dev/sdb` for use.

### Steps:
1. Create a partition:
   ```bash
   sudo fdisk /dev/sdb
   ```
   - Press `n` → `p` → Accept defaults → `w`.
   
2. Format it as `ext4`:
   ```bash
   sudo mkfs.ext4 /dev/sdb1
   ```

---

## 2. Mount the Disk

### Steps:
1. Create a mount point:
   ```bash
   sudo mkdir /mnt/smbshare
   ```
2. Get the UUID:
   ```bash
   sudo blkid /dev/sdb1
   ```
3. Add to `/etc/fstab`:
   ```
   UUID=xxxx /mnt/smbshare ext4 defaults 0 2
   ```
4. Mount the disk:
   ```bash
   sudo mount -a
   ```

---

## 3. Install and Configure Samba

### Install Samba:
```bash
sudo apt update
sudo apt install samba -y
```

### Configure Samba:
1. Edit `/etc/samba/smb.conf`:
   ```bash
   sudo nano /etc/samba/smb.conf
   ```

## 4. Create User-Specific Shares

### Steps:
1. Add users:
   ```bash
   sudo useradd -m mom
   sudo passwd <password> mom
   ```
   Repeat for others (`dad`, `sister`, etc.).

2. Create directories:
   ```bash
   sudo mkdir /mnt/smbshare/mom
   sudo mkdir /mnt/smbshare/dad
   ```

3. Set permissions:
   ```bash
   sudo chown mom:mom /mnt/smbshare/mom
   sudo chmod 700 /mnt/smbshare/mom
   ```

4. Update Samba configuration:
   ```ini
   [mom]
   path = /mnt/smbshare/mom
   valid users = mom
   read only = no
   browsable = yes
   guest ok = no
   ```

5. Restart Samba:
   ```bash
   sudo systemctl restart smbd
   ```

---

## 5. Shared Directory for All Users

### Steps:
1. Create the directory:
   ```bash
   sudo mkdir /mnt/smbshare/shared
   ```

2. Create a group and add users:
   ```bash
   sudo groupadd allusers
   sudo usermod -a -G allusers mom
   sudo usermod -a -G allusers dad
   ```

3. Set permissions:
   ```bash
   sudo chown :allusers /mnt/smbshare/shared
   sudo chmod 775 /mnt/smbshare/shared
   sudo chmod g+s /mnt/smbshare/shared
   ```

4. Update Samba configuration:
   ```ini
   [shared]
   path = /mnt/smbshare/shared
   valid users = @allusers
   read only = no
   browsable = yes
   guest ok = no
   ```

5. Restart Samba:
   ```bash
   sudo systemctl restart smbd
   ```

---

## 6. Testing the Configuration

### On Windows:
1. Open Explorer.
2. Enter `\\<server-ip>\SMBShare`.

### On Linux:
1. Test with `smbclient`:
   ```bash
   smbclient //<server-ip>/SMBShare -U your_username
   ```
