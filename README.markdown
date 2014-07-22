**BGI** Budget Gateway Interface
=======================================================================

Created by Ethan Joachim Eldridge  
License: MIT

What is this?
-----------------------------------------------------------------------

**BGI** is a static website with a CGI backend powered by C. You might
ask: "Why C?" The short answer is: __I love C__, the slightly longer
answer is that: __I wanted to see if I could__. 

How do I use it?
-----------------------------------------------------------------------

**Install qdecoder**  
You need to install [qdecoder] in order to be able to parse and handle
the CGI requests. qDecoder was chosen after reviewing the various
libraries [listed here]. To install it, download the tarball and untar
it `tar -xzvf <tarballname>` then run `./configure` and `make install`,
you may need to run the `make` command as your super user.

**Setup WebServer**  
Next, setup Apache for CGI. An example configuration might look like this:

	<VirtualHost *:80>
	         ServerName www.bgi.dev
	         DocumentRoot /home/user/programs/bgi/www
	         <Directory />
	                 Options Indexes
	                 AllowOverride None
	         </Directory>
	         Alias /bgi /home/user/programs/bgi/bin
	         <Directory />
	                 AddHandler cgi-script .cgi
	                 AllowOverride None
	                 Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
	                 Order allow,deny
	                 Allow from all
	         </Directory>
	 
	        ErrorLog ${APACHE_LOG_DIR}/error.cgi.log
	        LogLevel warn

	</VirtualHost>

Note that it is important to setup both static front end (www) and the CGI
directory (bgi). One of the biggest 'gotchas' is the permissions on the data
directory. If after attempting to create an account the data directory does
not exist or repeatedly fails, then you should check the permissions of the 
place specified by the `config.h` file and make sure it is writable by apache. 
One of the best guides for doing so is contained in [this S.O. question]


**Setup Your Config file**  
A sample config file is included in the headers, the best way to setup a config
file is to copy this file, edit it, and rename it to config.h. If you do not
want to do this manually, you can run `make config` and it will save you the grunt
work of typing the commands to do so. Note that if a config file already exists
it will be moved to config.h.bak

**Compile the Scripts**  
All you need to do is run `make` from the root of the repository.

**Compile the WebSite**  
What you say? Compile the website? Yes. The HTML is generated via [HarpJs] and
you'll need to have that installed. Simply update the `harp.json` file with the
proper domain name and run a `harp compile`. It shouldn't take long and then you'll
have the front end setup and ready.

**Setup your hosts file (if local)**  
On most linux systems the hosts file is at /etc/hosts, on windows it is in system32
I believe (it's been a while since I've been on windozer). Add in the name that you
have placed into your config's `BASE_URL` definition pointing to`127.0.0.1`

**Technologies used**  
 
 - [HarpJs]
 - [Chart.js]
 - [Leaflet.js]
 - [qdecoder]
 - C


**Use it!**  
<img src="http://static1.ethanjoachimeldridge.info/tech-blog/index.png" style="max-width: 778px" />

<img src="http://static1.ethanjoachimeldridge.info/tech-blog/welcome.png" style="max-width: 778px" />

<img src="http://static1.ethanjoachimeldridge.info/tech-blog/metrics.png" style="max-width: 778px" />

<img src="http://static1.ethanjoachimeldridge.info/tech-blog/map.png" style="max-width: 778px" />



[qdecoder]:http://www.qdecoder.org/wiki/qdecoder
[listed here]:http://cgi.resourceindex.com/programs_and_scripts/c_and_c++/libraries_and_classes/
[this S.O. question]:http://serverfault.com/questions/124800/how-to-setup-linux-permissions-for-the-www-folder
[HarpJs]:http://harpjs.com
[Chart.js]:https://github.com/nnnick/Chart.js
[Leaflet.js]:http://Leafletjs.com