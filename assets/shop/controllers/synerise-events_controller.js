import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  ready = false;
  static values = {
    onConnect: String,
    campaignId: String,
    correlationId: String,
    products: Array,
  };

  connect() {
    if (this[this.onConnectValue] instanceof Function)
      this.#onConnectFunction();
  }

  #onConnectFunction() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          this[this.onConnectValue]();
          observer.disconnect();
        }
      });
    });

    observer.observe(this.element);
  }

  recommendationClick(event) {
    SR.event.recommendationClick(
      {
        campaignId: this.campaignIdValue,
        correlationId: this.correlationIdValue,
        item: event.params.item,
      },
      "Recommended item was clicked"
    );
  }

  recommendationView() {
    SR.event.recommendationView(
      {
        campaignId: this.campaignIdValue,
        correlationId: this.correlationIdValue,
        items: this.productsValue,
      },
      "Recommended items were displayed"
    );
  }
}
