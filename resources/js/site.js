// Public website: lightweight Alpine only (no Livewire, charts or jQuery)
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';

Alpine.plugin([collapse, focus, intersect]);

window.Alpine = Alpine;
Alpine.start();
