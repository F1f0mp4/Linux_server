To set up your Minecraft server from scratch with the desired version, follow these steps:

### **1. Create a New Directory for the Server**
Organize your files by creating a dedicated folder for the server.

```bash
mkdir ~/minecraft-server
cd ~/minecraft-server
```

---

### **2. Download the Fabric Installer**  
   Download the Fabric server installer script using `wget`:

   ```bash
   wget https://maven.fabricmc.net/net/fabricmc/fabric-installer/0.11.2/fabric-installer-0.11.2.jar -O fabric-installer.jar
   ```

---

### **3. Install the Fabric Server**  
   Run the Fabric installer to set up the server:

   ```bash
   java -jar fabric-installer.jar server -downloadMinecraft
   ```

   This will:
   - Download the necessary Minecraft version.
   - Set up a Fabric modded server.


      Edit the `eula.txt` file generated in the server directory and set:
   ```txt
   eula=true
   ```

---

5. **Download Mods**  
   Download the mods you want from trusted sources like:
   - [CurseForge](https://www.curseforge.com/minecraft/mc-mods)
   - [Modrinth](https://modrinth.com/)

   Place the downloaded `.jar` files into the `mods/` directory of the server.

---

6. **Start the Server**  
   Start the Fabric server with:
   ```bash
   java -Xmx1024M -Xms1024M -jar fabric-server-launch.jar nogui
   ```

---

7. **Share the Required Mods with Players**  
   To make it simple for players:
   - Create a `.zip` containing all the mods you used and share it.
   - Alternatively, create a modpack for CurseForge or ATLauncher for automated installation.

---

### **Optional: Automate Installation**

To mimic the simplicity of downloading the server `.jar`:
1. Create a script to automate the setup:
   ```bash
   #!/bin/bash
   wget https://maven.fabricmc.net/net/fabricmc/fabric-installer/0.11.2/fabric-installer-0.11.2.jar -O fabric-installer.jar
   java -jar fabric-installer.jar server -downloadMinecraft
   echo "eula=true" > eula.txt
   mkdir mods
   echo "Download your mods and place them in the 'mods/' folder."
   ```

2. Save it as `setup_modded_server.sh` and make it executable:
   ```bash
   chmod +x setup_modded_server.sh
   ```

3. Run the script:
   ```bash
   ./setup_modded_server.sh
   ```

---

### **Switching Versions**  
To change the server version:
1. Use the `fabric-installer.jar` again:
   ```bash
   java -jar fabric-installer.jar server -mcversion <desired_version> -downloadMinecraft
   ```
   Replace `<desired_version>` with the Minecraft version you want, e.g., `1.21.0`.

2. Re-download mods compatible with the new version.

---
