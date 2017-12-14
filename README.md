A highly customizable Admin Panel and its API (under constant development) using SLIM FRAMEWORK .

Requires: 
1) Apache server with motd_rewrite enabled.
2) Slim 3.0
3) Any Templating engine you want (here PHPView is used)

Project Structure :-

a) "classes" folder: Here all your PHP logical part goes. One can easily integrate his own "Session and other authentication/security" libraries here.Initially a very basic SessionManager class is used.
b) "admin" folder: Path to admin panel.
c) "API" folder: Path to access API

One can use any bootstrap templates. All of the folder are self-explanatory. 

Note : 
1) For different browsers ,different versions of "DataTable".js plugin is included. Don't worry if it does not works at first,try including the correct source.
2) Multiple "DataTable".css included. Add/remove upto your wish. 

To be added:-

1) HTTP Caching
2) CSRF Protection
3) Login API to integrate with android_apps using JSON Token Authentication.
