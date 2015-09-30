/**
 * Created by ventureoak on 11-06-2015.
 */
var newsApp = angular.module('newsAppConfig',[]);

newsApp.factory('config', function() {
    var apiBase = "http://api.dailynews.dev";


    return {
        routes:{
            api:{
                login: function(username){
                    return apiBase+("/user/:username/".replace(/:username/,username));
                },
                source:function(username){
                    return apiBase+("/user/:username/source".replace(/:username/,username));
                },
                news: function(username){
                    return apiBase+("/user/:username/news".replace(/:username/,username));
                }
            }
        },
        events:{
            flash : {
                push: 'dailynews:flash_message:push'
            }
        },
        timeout:{
            flash: 2000
        }
    };

});
