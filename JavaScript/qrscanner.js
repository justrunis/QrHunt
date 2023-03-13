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
let hasFinished = false;
let stream = null;

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia && hasFinished == false) {
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
          hasFinished = true;
          video.style.display = 'none';
          stream.getTracks().forEach(function(track) {
            track.stop();
          });
        }
      }, 100);
    }, false);
  }).catch(function(error) {
    console.error('Failed to access device camera', error);
  });
}