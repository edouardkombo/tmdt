
    var express         = require('express');
    var app             = express();                               // create our app w/ express
    var http            = require('http');
    //var mongoose = require('mongoose');                     // mongoose for mongodb
    var morgan          = require('morgan');             // log requests to the console (express4)
    var bodyParser      = require('body-parser');    // pull information from HTML POST (express4)
    var methodOverride  = require('method-override'); // simulate DELETE and PUT (express4)

    // configuration =================
    function createVirtualHost(domainName, dirPath) {
        var vhost = express();
        //parses request body and populates request.body
        vhost.use( express.bodyParser() );
        //checks request.body for HTTP method overrides
        vhost.use( express.methodOverride() );
        //Where to serve static content
        vhost.use( express.static( dirPath ) );
        //Show errors
        vhost.use( express.errorHandler({ dumpExceptions: true, showStack: true }));

        return express.vhost(domainName, vhost);
    }


    //Create the virtual hosts
    var theMillionDollarTalk = createVirtualHost("www.themilliondollartalk.com", "themilliondollartalk");
    //Use the virtual hosts
    app.use(theMillionDollarTalk);


    //mongoose.connect('mongodb://node:node@mongo.onmodulus.net:27017/uwO3mypu');     // connect to mongoDB database on modulus.io   
    app.use(express.static(__dirname + '/'));                 // set the static files location /public/img will be /img for users
    app.use(morgan('dev'));                                         // log every request to the console
    app.use(bodyParser.urlencoded({'extended':'true'}));            // parse application/x-www-form-urlencoded
    app.use(bodyParser.json());                                     // parse application/json
    app.use(bodyParser.json({ type: 'application/vnd.api+json' })); // parse application/vnd.api+json as json
    app.use(methodOverride());
    app.use(require('prerender-node').set('prerenderToken', 'dLZx8VVyUQ5RquUPDkXE'));
    
    // listen (start app with node server.js) ======================================
    app.server = http.createServer(app);
    app.server.listen(8080);    
    console.log("App listening on port 8080");

    //Keep angularJs routing
    /*app.get('*', function(req, res) {
        res.sendfile('index.html'); // load the single view file (angular will handle the page changes on the front-end)
    });*/  