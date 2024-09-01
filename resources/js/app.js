import "./bootstrap";
import Alpine from "alpinejs";
import $ from "jquery";
import "@fortawesome/fontawesome-free/css/all.css";
import Swal from "sweetalert2";
import validate from "validate.js";

window.Alpine = Alpine;
window.$ = window.jQuery = $;
window.Swal = Swal;
window.validate = validate;

Alpine.start();
