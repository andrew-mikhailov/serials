(function (angular, global) {
  'use strict';

  angular.module('CinemaPortal')
    .service('watchService', ['$q', '$http', 'generalConf', function($q, $http, generalConf) {
      var watchService = this;

      watchService.getVideo = function(seasonId, videoId) {
        var deferred = $q.defer();

        $http.get(generalConf.basePath + '/api/episode/' + seasonId + '/' + videoId)
          .then(function(response) {
            deferred.resolve(response)
          });

        return deferred.promise;
      }
    }])
    .controller('WatchPageController', ['$rootScope', '$sce', '$routeParams', 'watchService',
      function ($rootScope, $sce, $routeParams, watchService) {
        var ctrl = this;

        ctrl.videoId = $routeParams.videoId;
        ctrl.seasonId = $routeParams.seasonId;
        ctrl.config = null;
        ctrl.isEnging = false;
        ctrl.nextVideo = false;
        ctrl.timer = 10;
        ctrl.countdown = 0;

        ctrl.getVideoInfo = function(seasonId, videoId) {
          $rootScope.loading = true;
          watchService.getVideo(seasonId, videoId)
            .then(function(response) {
              ctrl.nextVideo = response.nextVideo;

              ctrl.config = {
                autoHide: true,
                autoHideTime: 3000,
                autoPlay: true,
                loop: false,
                preload: "none",
                sources: [
                  {
                    src: $sce.trustAsResourceUrl("http://admin.serials.loc/video/1"),
                    type: "video/mp4"
                  }
                ],
                theme: "assets/js/vendor/videogular-themes-default/videogular.css",
                plugins: {
                  poster: "http://www.videogular.com/assets/images/videogular.png",
                  analytics: {
                    category: "Videogular",
                    label: "Main",
                    events: {
                      ready: true,
                      play: true,
                      pause: true,
                      stop: true,
                      complete: true,
                      progress: 10
                    }
                  }
                }
              };

              ctrl.isEnging = false;
              $rootScope.loading = false;
            });
        };

        ctrl.updateTime = function(currentTime, duration) {
          if (!ctrl.isEnging) {
            if (duration - currentTime <= 70) {
              ctrl.countdown = 10;
            }
          }
        };

        ctrl.timerFinished = function() {
          ctrl.isEnging = true;
          ctrl.videoId = ctrl.nextVideo.videoId;
          ctrl.seasonId = ctrl.nextVideo.seasonId;
          ctrl.getVideoInfo(ctrl.seasonId, ctrl.videoId);
        };

        ctrl.getVideoInfo(ctrl.seasonId, ctrl.videoId);
    }])

})(angular, window);
