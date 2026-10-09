// Portals (admin, instructor, student): Livewire (which bundles Alpine), charts, date pickers, uploads
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import './bootstrap';
import { Chart, registerables } from 'chart.js';
import flatpickr from 'flatpickr';
import Dropzone from 'dropzone';

Chart.register(...registerables);
Chart.defaults.font.family = '"Inter", sans-serif';
Chart.defaults.color = '#57574F';
Chart.defaults.plugins.tooltip.backgroundColor = '#111110';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.displayColors = false;

Dropzone.autoDiscover = false;

// Page scripts use these as globals
window.Chart = Chart;
window.flatpickr = flatpickr;
window.Dropzone = Dropzone;

Livewire.start();

const pickerArrows = {
  prevArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M5.4 10.8l1.4-1.4-4-4 4-4L5.4 0 0 5.4z" /></svg>',
  nextArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M1.4 10.8L0 9.4l4-4-4-4L1.4 0l5.4 5.4z" /></svg>',
};

const keepValue = (selectedDates, dateStr, instance) => { instance.element.value = dateStr; };
const addClass = (selectedDates, dateStr, instance) => {
  const customClass = instance.element.getAttribute('data-class');
  if (customClass) instance.calendarContainer.classList.add(customClass);
};

document.addEventListener('DOMContentLoaded', () => {
  const base = { static: true, monthSelectorType: 'static', defaultDate: null, onReady: addClass, onChange: keepValue, ...pickerArrows };

  flatpickr('.datepicker', { ...base, dateFormat: 'M j, Y' });
  flatpickr('.datetimepicker', { ...base, enableTime: true, dateFormat: 'M j, Y h:i K' });
  flatpickr('.timepicker', { ...base, enableTime: true, noCalendar: true, dateFormat: 'h:i K' });
});

// Rich-text editing (CKEditor 5) for textareas with id "editor"/"editor1" or class "rich-editor".
// window.CKEDITOR mimics the small part of the CKEditor 4 API older page scripts use
// (instances.<id>.setData/getData/on), queueing calls until the editor is ready.
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const makeInstance = (textarea) => {
  let editor = null;
  const queue = [];
  const run = (fn) => (editor ? fn(editor) : queue.push(fn));
  const instance = {
    setData: (data) => run((e) => e.setData(data ?? '')),
    getData: () => (editor ? editor.getData() : textarea.value),
    on: (event, callback) => run((e) => e.model.document.on('change:data', () => callback.call(instance))),
  };

  ClassicEditor.create(textarea, {
    licenseKey: 'GPL',
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo'],
  }).then((created) => {
    editor = created;
    // Keep the underlying textarea current so plain form posts and Alpine bindings see the text
    editor.model.document.on('change:data', () => editor.updateSourceElement());
    queue.splice(0).forEach((fn) => fn(editor));
  }).catch((error) => console.error(error));

  return instance;
};

window.CKEDITOR = { instances: {} };
document.querySelectorAll('textarea#editor, textarea#editor1, textarea.rich-editor').forEach((textarea) => {
  window.CKEDITOR.instances[textarea.id || `editor-${Object.keys(window.CKEDITOR.instances).length}`] = makeInstance(textarea);
});
