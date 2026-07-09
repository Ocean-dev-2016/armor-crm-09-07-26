importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.9.1/firebase-messaging.js');
/*Update this config*/
    var config = {
        apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
    authDomain: "cmk-crm.firebaseapp.com",
    projectId: "cmk-crm",
    storageBucket: "cmk-crm.appspot.com",
    messagingSenderId: "345899882377",
    appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
    measurementId: "G-2TS49WRQ29"

    };
  firebase.initializeApp(config);

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.data.title;
  const notificationOptions = {
    body: payload.data.body,
  icon: 'https://rajcrm.com/images/raj_logo.png',
  image: 'https://rajcrm.com/images/raj_logo.png'
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});
// [END background_handler]