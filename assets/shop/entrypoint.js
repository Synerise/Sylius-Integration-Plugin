import { Application } from "@hotwired/stimulus";
import EventsController from "./controllers/synerise-events_controller";

const app = window.Stimulus || Application.start();

app.register("synerise-events", EventsController);

export { app };
