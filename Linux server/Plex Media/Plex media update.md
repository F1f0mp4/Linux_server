#### Plex media update ####

wget https://downloads.plex.tv/plex-media-server-new/1.41.6.9685-XXXXXX/debian/plexmediaserver_latest.deb  

// instead of XXXXX add version from https://www.plex.tv/media-server-downloads/?cat=computer&plat=linux#plex-media-server


sudo dpkg -i plexmediaserver_latest.deb 

sudo systemctl restart plexmediaserver

