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

You need to install [qdecoder] in order to be able to parse and handle
the CGI requests. qDecoder was chosen after reviewing the various
libraries [listed here]. To install it, download the tarball and untar
it `tar -xzvf <tarballname>` then run `./configure` and `make install`,
you may need to run the `make` command as your super user.





[qdecoder]:http://www.qdecoder.org/wiki/qdecoder
[listed here]:http://cgi.resourceindex.com/programs_and_scripts/c_and_c++/libraries_and_classes/
