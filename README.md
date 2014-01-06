Imperium sdk
============================

Steps for installing the sdk
-----------------------------

* Download the code and copy the directory into
a directory for example:
"/tmp/rest_web_service"

* Configure the lighttpd.conf and add the following line:
```
restImperium="/tmp/rest_web_service/web"
$HTTP["host"] == "192.168.0.10" {
   server.document-root = "/tmp/rest_web_service/web"
   url.rewrite-once = (
      # configure some static files
      "^/css/.+" => "$0",
      "^/img/.+" => "$0",
      "^/js/.+" => "$0",
      "^/assets/.+" => "$0",
      "^/favicon\.ico$" => "$0",
      "^(/[^\?]*)(\?.*)?" => "/index.php$1$2"
   )
}
```
* Inside the directory config copy the
parameters.ini.sample with the name parameters.ini
and set the parameters.

* Inside the config copy the file log4php.ini.sample
to log4php.ini and set the values for you.

* Read the examples in the example directory and use it.
