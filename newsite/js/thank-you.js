$(document).ready(funciton(){

    // change page title if this is the DMT thank you page
    if (window.location.href.indexOf('dmt-thank-you') >= 0) {
      var delaySec = 3; //number of seconds to wait
      var downloadUrl = 'http://bit.ly/2kkpif7';
      function triggerDownload() {
        window.location.href = downloadUrl;
      }
      setTimeout(triggerDownload(), delaySec * 1000);
    }
    
    // change page title if this is the Redesign E-book thank you page
    if (window.location.href.indexOf('rock-your-redesign') >= 0) {
      var delaySec = 3; //number of seconds to wait
      var downloadUrl = 'http://go.influencehealth.com/Rock-Your-Redesign';
      function triggerDownload() {
        window.location.href = downloadUrl;
      }
      setTimeout(triggerDownload(), delaySec * 1000);
    }

      // change page title if this is the CX white paper thank you page
    if (window.location.href.indexOf('cxp-thank-you') >= 0) {
        var delaySec = 3; //number of seconds to wait
        var downloadUrl = 'http://bit.ly/2pDiH1w';
        function triggerDownload() {
            window.location.href = downloadUrl;
        }
        setTimeout(triggerDownload(), delaySec * 1000);
    }

});