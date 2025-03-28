To set up your Minecraft server from scratch with the desired version, follow these steps:

### **1. Create a New Directory for the Server**
Organize your files by creating a dedicated folder for the server.

```bash
mkdir ~/minecraft-server
cd ~/minecraft-server
```

---

### **2. Download the Desired Minecraft Server Version**
Visit the [Minecraft Server Download Page](https://www.minecraft.net/en-us/download/server) or search for older server versions at [Mojang's archive](https://mcversions.net/).

Replace `<version>` with your desired version (e.g., `1.21.0`).

```bash
wget https://piston-data.mojang.com/v1/objects/<server_version_hash>/server.jar
```

Alternatively, if you're unsure about the hash, download it manually from [mcversions.net](https://mcversions.net/), and move it to `~/minecraft-server`.

---

### **3. Agree to the EULA**
Run this command to create the `eula.txt` file:

```bash
echo "eula=true" > eula.txt
```

---

### **4. Configure `server.properties`**
Create or edit the `server.properties` file for your desired settings:

```bash
nano server.properties
```

Example configuration:
```properties
# Server basics
motd=  \u00A7aWelcome to the \u00A7eLand of Ooo\u00A7a! \u00A7b| \u00A76Adventure Time!\u00A7r\n        \u00A79Come join the fun, \u00A75Heroes of Ooo!\u00A7r\n                        
server-port=25565
server-name=Land of Ooo
server-ip=

# Game settings
gamemode=survival
difficulty=easy
max-players=5
online-mode=true
enable-whitelist=true

# World settings
level-seed=28997391
view-distance=32
allow-nether=true
```

Save and exit (`Ctrl + O`, `Enter`, `Ctrl + X`).

---

### **5. Run the Server**
Start the server with the specified version and memory allocation.

```bash
java -Xmx8192M -Xms8192M -jar server.jar nogui
```

### **6. Change the Version**
If you want to switch to a different Minecraft version:
1. Stop the server.
   ```bash
   pkill java
   ```
2. Download the server JAR for the desired version (repeat **Step 3**).
3. Replace the old `server.jar` with the new one:
   ```bash
   rm server.jar
   wget https://piston-data.mojang.com/v1/objects/<new_version_hash>/server.jar
   ```

---

### **7. Whitelist Players**
To add players to the whitelist:
1. Run the server once to generate the necessary files.
2. Add player names to the `whitelist.json` file:
   ```bash
   nano whitelist.json
   ```
   Example:
   ```json
   [
       {
           "uuid": "player-uuid",
           "name": "player-name"
       }
   ]
   ```
   Alternatively, use the command:
   ```bash
   whitelist add player-name
   ```

---
