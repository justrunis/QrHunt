// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * @module    mod_qrhunt/JavaScript
 * @package   mod_qrhunt
 * @copyright Justinas Runevičius <justinas.runevicius@distance.ktu.lt>
 * @author Justinas Runevičius <justinas.runevicius@distance.ktu.lt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const result = document.getElementById('result');
const startCameraBtn = document.getElementById('start-camera');

const videoContainer = document.getElementById('video-container');
const currentUrl = window.location.href; // get current URL
const urlParams = new URLSearchParams(window.location.search); // parse query string
const currentId = urlParams.get('id'); // get value of id parameter

// construct the URL for the POST request
const url = `${currentUrl.substring(0, currentUrl.lastIndexOf('/'))}/play.php?id=${currentId}`;

let hasFinished = false;
let stream = null;

console.log(navigator.mediaDevices);
console.log(navigator.mediaDevices.getUserMedia);
console.log(hasFinished);

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia && hasFinished == false) {
  startCameraBtn.style.display = "block"; // show the start camera button
  startCameraBtn.addEventListener('click', function() {
    videoContainer.style.display = "block";
    startCameraBtn.style.display = "none"; // hide the start camera button
    navigator.mediaDevices.getUserMedia({ video: true }).then(function(mediaStream) {
      stream = mediaStream;
      video.srcObject = stream;
      video.play();
      console.log('Displaying the camera feed')
      video.addEventListener('play', function() {
        const ctx = canvas.getContext('2d');
        setInterval(function() {
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const code = jsQR(imageData.data, imageData.width, imageData.height);
          if (code) {
            console.log('QR code detected:', code.data);
            result.innerText = code.data;
            if (video.style.display !== 'none') {
              video.style.display = 'none';
              stream.getTracks().forEach(function(track) {
                track.stop();
              });
            }
            $('#user_answer').val(code.data);
          }
        }, 100);
      }, false);
    }).catch(function(error) {
      console.error('Failed to access device camera', error);
    });
  });
}
