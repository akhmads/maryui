import "./bootstrap";
import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";
import mask from "@alpinejs/mask";

Alpine.plugin(mask);

Livewire.start();
