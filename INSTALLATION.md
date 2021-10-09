# INSTALLATION
   
  Installing Invenktory is more straightforward than what it could appear..   
  
  First, if you use Nginx as reverse proxy just point the root of your web app to /path/to/YourInvenktory/Public/static   
  where the static content is located.
  
  Apache instead should have DocumentRoot pointing to /path/to/YourInvenktory/Public .   
  
  If you don't use Nginx as reverse proxy consider to merge /Public/static folder with /Public .   
  
  Dan
