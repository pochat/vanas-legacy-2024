# vanas-legacy-2024
VANAS-LEGACY-2024

#instalation additional server

- sudo add-apt-repository ppa:jonathonf/ffmpeg-4
- sudo apt install ffmpeg
- verify the existence of the ffmpeg folder in the path /var/www/html/vanas/fame
- https://johnvansickle.com/ffmpeg/ download the version for your current operating system ubuntu arm, amd etc. and replace the ffmpeg folder.

# NFS Storage link

- run in terminal, create link
 
1. ln -s /efs/data2/vanas/vanas_videos/campus /var/www/html/vanas/vanas_videos/campus
2. ln -s /efs/data2/vanas/attachments /var/www/html/vanas/attachments
3. ln -s /efs/vanas_uploads/students/sketches /var/www/html/vanas/modules/students/sketches
4. ln -s /efs/vanas_uploads/teachers/images /var/www/html/vanas/modules/teachers/images
5. ln -s /efs/vanas_uploads/students/images /var/www/html/vanas/modules/students/images
6. ln -s /efs/vanas_uploads/students/videos /var/www/html/vanas/modules/students/videos
7. ln -s /efs/vanas_uploads/common/new_campus/upload /var/www/html/vanas/modules/common/new_campus/upload
8. ln -s /efs/vanas_uploads/common/new_campus/images /var/www/html/vanas/modules/common/new_campus/images


#Path Images

- migrate the following directories. to the efs if necessary

- /var/www/html/vanas/attachments
- /var/www/html/vanas/modules/students/sketches
- /var/www/html/vanas/modules/teachers/images
- /var/www/html/vanas/modules/students/images
- /var/www/html/vanas/modules/common/new_campus/upload
- /var/www/html/vanas/modules/students/videos

#install Node.js
- copy vanas_node directories
- check correct ssl paths database connection username and password
- Go inside the folder and execute:
- sudo forever start server.js  