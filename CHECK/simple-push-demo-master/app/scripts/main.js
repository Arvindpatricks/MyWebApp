

/**
 *
 *  Web Starter Kit
 *  Copyright 2014 Google Inc. All rights reserved.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *    https://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License
 *
 */


export default class PushClient {

  constructor(stateChangeCb, subscriptionUpdate) {
    this._stateChangeCb = stateChangeCb;
    this._subscriptionUpdate = subscriptionUpdate;

    this.state = {
      UNSUPPORTED: {
        id: 'UNSUPPORTED',
        interactive: false,
        pushEnabled: false
      },
      INITIALISING: {
        id: 'INITIALISING',
        interactive: false,
        pushEnabled: false
      },
      PERMISSION_DENIED: {
        id: 'PERMISSION_DENIED',
        interactive: false,
        pushEnabled: false
      },
      PERMISSION_GRANTED: {
        id: 'PERMISSION_GRANTED',
        interactive: true
      },
      PERMISSION_PROMPT: {
        id: 'PERMISSION_PROMPT',
        interactive: true,
        pushEnabled: false
      },
      ERROR: {
        id: 'ERROR',
        interactive: false,
        pushEnabled: false
      },
      STARTING_SUBSCRIBE: {
        id: 'STARTING_SUBSCRIBE',
        interactive: false,
        pushEnabled: true
      },
      SUBSCRIBED: {
        id: 'SUBSCRIBED',
        interactive: true,
        pushEnabled: true
      },
      STARTING_UNSUBSCRIBE: {
        id: 'STARTING_UNSUBSCRIBE',
        interactive: false,
        pushEnabled: false
      },
      UNSUBSCRIBED: {
        id: 'UNSUBSCRIBED',
        interactive: true,
        pushEnabled: false
      }
    };

    if (!('serviceWorker' in navigator)) {
      this._stateChangeCb(this.state.UNSUPPORTED);
      return;
    }

    if (!('PushManager' in window)) {
      this._stateChangeCb(this.state.UNSUPPORTED);
      return;
    }

    if (!('permissions' in navigator)) {
      this._stateChangeCb(this.state.UNSUPPORTED);
      return;
    }

    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
      this._stateChangeCb(this.state.UNSUPPORTED);
      return;
    }

    navigator.serviceWorker.ready.then(() => {
      this._stateChangeCb(this.state.INITIALISING);
      this.setUpPushPermission();
    });
  }

  _permissionStateChange(permissionState) {
    // console.log('PushClient.permissionStateChange(): ', permissionState);
    // If the notification permission is denied, it's a permanent block
    switch (permissionState.state) {
    case 'denied':
      this._stateChangeCb(this.state.PERMISSION_DENIED);
      break;
    case 'granted':
      this._stateChangeCb(this.state.PERMISSION_GRANTED);
      break;
    case 'prompt':
      this._stateChangeCb(this.state.PERMISSION_PROMPT);
      break;
    default:
      break;
    }
  }

  setUpPushPermission() {
    // console.log('PushClient.setUpPushPermission()');
    navigator.permissions.query({name: 'push', userVisibleOnly: true})
    .then((permissionState) => {
      // Set the initial state
      this._permissionStateChange(permissionState);

      // Handle Permission State Changes
      permissionState.onchange = () => {
        this._permissionStateChange(this);
      };

      // Check what the current push state is
      return navigator.serviceWorker.ready;
    })
    .then((serviceWorkerRegistration) => {
      // Let's see if we have a subscription already
      return serviceWorkerRegistration.pushManager.getSubscription();
    })
    .then((subscription) => {
      if (!subscription) {
        // NOOP since we have no subscription and the permission state
        // will inform whether to enable or disable the push UI
        return;
      }

      this._stateChangeCb(this.state.SUBSCRIBED);

      // Update the current state with the
      // subscriptionid and endpoint
      this._subscriptionUpdate(subscription);
    })
    .catch((err) => {
      console.log('PushClient.setUpPushPermission() Error', err);
      this._stateChangeCb(this.state.ERROR, err);
    });
  }

  subscribeDevice() {
    console.log('PushClient.subscribeDevice()');

    this._stateChangeCb(this.state.STARTING_SUBSCRIBE);


    // We need the service worker registration to access the push manager
    navigator.serviceWorker.ready
    .then((serviceWorkerRegistration) => {
      return serviceWorkerRegistration.pushManager.subscribe(
        {userVisibleOnly: true}
      );
    })
    .then((subscription) => {
      this._stateChangeCb(this.state.SUBSCRIBED);
      this._subscriptionUpdate(subscription);
    })
    .catch((subscriptionErr) => {
      console.log('PushClient.subscribeDevice() Error', subscriptionErr);

      // Check for a permission prompt issue
      return navigator.permissions.query({name: 'push', userVisibleOnly: true})
      .then((permissionState) => {
        this._permissionStateChange(permissionState);

        // window.PushDemo.ui.setPushChecked(false);
        if (permissionState.state !== 'denied' &&
        permissionState.state !== 'prompt') {
          // If the permission wasnt denied or prompt, that means the
          // permission was accepted, so this must be an error
          this._stateChangeCb(this.state.ERROR, subscriptionErr);
        }
      });
    });
  }

  unsubscribeDevice() {
    console.log('PushClient.unsubscribeDevice()');
    // Disable the switch so it can't be changed while
    // we process permissions
    // window.PushDemo.ui.setPushSwitchDisabled(true);

    this._stateChangeCb(this.state.STARTING_UNSUBSCRIBE);

    navigator.serviceWorker.ready
    .then((serviceWorkerRegistration) => {
      return serviceWorkerRegistration.pushManager.getSubscription();
    })
    .then((pushSubscription) => {
      // Check we have everything we need to unsubscribe
      if (!pushSubscription) {
        this._stateChangeCb(this.state.UNSUBSCRIBED);
        this._subscriptionUpdate(null);
        return;
      }

      // TODO: Remove the device details from the server
      // i.e. the pushSubscription.subscriptionId and
      // pushSubscription.endpoint
      return pushSubscription.unsubscribe()
      .then(function(successful) {
        if (!successful) {
          // The unsubscribe was unsuccessful, but we can
          // remove the subscriptionId from our server
          // and notifications will stop
          // This just may be in a bad state when the user returns
          console.error('We were unable to unregister from push');
        }
      })
      .catch(function(e) { });
    })
    .then(() => {
      this._stateChangeCb(this.state.UNSUBSCRIBED);
      this._subscriptionUpdate(null);
    })
    .catch((e) => {
      console.error('Error thrown while revoking push notifications. ' +
        'Most likely because push was never registered', e);
    });
  }
}

var API_KEY = 'AIzaSyBBh4ddPa96rQQNxqiq_qQj7sq1JdsNQUQ';

// Define a different server URL here if desire.
var PUSH_SERVER_URL = '';


function updateUIForPush(pushToggleSwitch) {
  // This div contains the UI for CURL commands to trigger a push
  var sendPushOptions = document.querySelector('.js-send-push-options');

  var stateChangeListener = function(state, data) {
    // console.log(state);
    if (typeof(state.interactive) !== 'undefined') {
      if (state.interactive) {
        pushToggleSwitch.enable();
      } else {
        pushToggleSwitch.disable();
      }
    }

    if (typeof(state.pushEnabled) !== 'undefined') {
      if (state.pushEnabled) {
        pushToggleSwitch.on();
      } else {
        pushToggleSwitch.off();
      }
    }

    switch (state.id) {
    case 'ERROR':
      console.error(data);
      showErrorMessage(
        'Ooops a Problem Occurred',
        data
      );
      break;
    default:
      break;
    }
  };

  var subscriptionUpdate = (subscription) => {
    console.log('subscriptionUpdate: ', subscription);
    if (!subscription) {
      // Remove any subscription from your servers if you have
      // set it up.

      sendPushOptions.style.opacity = 0;
      return;
    }

    // We should figure the GCM curl command
    var produceGCMProprietaryCURLCommand = function() {
      var curlEndpoint = 'https://android.googleapis.com/gcm/send';
      var endpointSections = subscription.endpoint.split('/');
      var subscriptionId = endpointSections[endpointSections.length - 1];
      var curlCommand = 'curl --header "Authorization: key=' +
        API_KEY + '" --header Content-Type:"application/json" ' +
        curlEndpoint + ' -d "{\\"registration_ids\\":[\\"' +
        subscriptionId + '\\"]}"';
      return curlCommand;
    };

    var produceWebPushProtocolCURLCommand = function() {
      var curlEndpoint = subscription.endpoint;
      var curlCommand = 'curl --request POST ' + curlEndpoint;
      return curlCommand;
    };

    var curlCommand;
    if (subscription.endpoint.indexOf(
      'https://android.googleapis.com/gcm/send') === 0) {
      curlCommand = produceGCMProprietaryCURLCommand();
    } else {
      curlCommand = produceWebPushProtocolCURLCommand();
    }

    var curlCodeElement = document.querySelector('.js-curl-code');
    curlCodeElement.innerHTML = curlCommand;

    // Code to handle the XHR
    var sendPushViaXHRButton = document.querySelector('.js-send-push-button');
    sendPushViaXHRButton.addEventListener('click', function(e) {
      var headers = new Headers();
      headers.append('Content-Type', 'application/json');

      fetch(PUSH_SERVER_URL + '/send_web_push', {
        method: 'post',
        headers: headers,
        body: JSON.stringify(subscription)
      }).then(function(response) {
        return response.json();
      })
      .then((responseObj) => {
        if (!responseObj.success) {
          throw new Error('Unsuccessful attempt to send push message');
        }
      })
      .catch(function(err) {
        console.log('Fetch Error :-S', err);
      });
    });

    // Display the UI
    sendPushOptions.style.opacity = 1;
  };

  var pushClient = new PushClient(
    stateChangeListener,
    subscriptionUpdate
  );

  document.querySelector('.js-push-toggle-switch > input')
  .addEventListener('click', function(event) {
    // Inverted because clicking will change the checked state by
    // the time we get here
    if (!event.target.checked) {
      pushClient.unsubscribeDevice();
    } else {
      pushClient.subscribeDevice();
    }
  });

  // Check that service workers are supported
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js', {
      scope: './'
    });
  } else {
    showErrorMessage(
      'Service Worker Not Supported',
      'Sorry this demo requires service worker support in your browser. ' +
      'Please try this demo in Chrome or Firefox Nightly.'
    );
  }
}


// Below this comment is code to initialise a material design lite view.
var toggleSwitch = document.querySelector('.js-push-toggle-switch');
toggleSwitch.initialised = false;

// This is to wait for MDL initialising
document.addEventListener('mdl-componentupgraded', function() {
  if (toggleSwitch.initialised) {
    return;
  }

  toggleSwitch.initialised = toggleSwitch.classList.contains('is-upgraded');
  if (!toggleSwitch.initialised) {
    return;
  }

  var pushToggleSwitch = toggleSwitch.MaterialSwitch;

  updateUIForPush(pushToggleSwitch);
});

function showErrorMessage(title, message) {
  var errorContainer = document.querySelector('.js-error-message-container');

  var titleElement = errorContainer.querySelector('.js-error-title');
  var messageElement = errorContainer.querySelector('.js-error-message');
  titleElement.textContent = title;
  messageElement.textContent = message;
  errorContainer.style.opacity = 1;

  var pushOptionsContainer = document.querySelector('.js-send-push-options');
  pushOptionsContainer.style.display = 'none';
}
