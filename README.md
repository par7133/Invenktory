# Invenktory
Hello and welcome to Invenktory!   
   
Invenktory is a light and simple software on premise to track your devices.     
   
Invenktory is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.    
   
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.   
   
As you are going to run Invenktory in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:

1. Check the permissions of your "Inventory" folder in your web app private path; and set its path in the config file.   
2. In the Inventory path create a ".INVsampledir" folder and give to this folder the write permission. This folder will be the sample folder to copy from new folders inside the inventory path.   
3. Likewise, in the Inventory path must exist ".INVsamplefile.xml" and give to this file the write permission. This file will be the sample file to copy from new inventory files.   
4. Configure the max devices per xml file as required (default: 10).   
5. Configure the max history items and max recall history items as required (default: 50).  
  
Invenktory understands a limited set of commands with a far limited set of parameters:  
cd, cd.., cp, edit, help, ls, mv, pwd, show  	   

Go ahead by typeing in your password and your command.   

In edit mode press [CTRL]+[X] to exit or [CTRL]+[S] to save.   

For any need of software additions, plugins and improvements please write to <a href="mailto:info@5mode.com">info@5mode.com</a>  

To help please donate by clicking <a href="https://gaox.io/l/dona1">https://gaox.io/l/dona1</a> and filling the form. 

### Screenshot:

 ![Http Console in action](/Public/static/res/screenshot1.jpg)

Feedback: <a href="mailto:code@gaox.io">code@gaox.io</a>

