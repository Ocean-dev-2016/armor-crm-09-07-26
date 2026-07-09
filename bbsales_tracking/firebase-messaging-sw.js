importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-messaging.js');
/*Update this config*/
    var config = {
        apiKey: "AIzaSyBpVBqscJrEYRS1PsDVHLelB4uWBI06g8E",
        authDomain: "craftbox-5d2bb.firebaseapp.com",
        databaseURL: "https://craftbox-5d2bb.firebaseio.com",
        projectId: "craftbox-5d2bb",
        storageBucket: "craftbox-5d2bb.appspot.com",
        messagingSenderId: "662801906103",
        appId: "1:662801906103:web:ba8add5e0bbedecfc09032",
        measurementId: "G-ZQLXEW4E2Y"
    };
  firebase.initializeApp(config);

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.data.title;
  const notificationOptions = {
    body: payload.data.body,
  icon: 'http://localhost/gcm-push/img/icon.png',
  image: 'http://localhost/gcm-push/img/d.png'
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});
// [END background_handler]