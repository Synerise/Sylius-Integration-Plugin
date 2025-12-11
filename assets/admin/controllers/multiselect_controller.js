import { Controller } from "@hotwired/stimulus";
import TomSelect from "tom-select";

export default class extends Controller {
  static values = {
    options: {
      type: Object,
      default: {},
    },
  };

  connect() {
    const selectInput = this.element;

    const prepareChoices = (selected) => {
      const values = new Set();
      Array.from(selectInput.options).forEach((option) => {
        if (values.has(option.value)) return option.remove();
        values.add(option.value);
        if (selected.includes(option.value)) option.selected = "selected";
      });
    };

    const defaultOptions = {
      options: selectInput.options,
      plugins: {
        remove_button: {
          className:
            "remove h-4 d-flex align-items-center justify-content-center",
        },
      },
      render: {
        option: (data, escape) => {
          return `<div class="d-flex align-items-center">
          <span>${escape(data.text)}</span>
          </div>`;
        },
        item: (data, escape) => {
          return `<div class="d-flex badge bg-blue-lt rounded-2">
            <span>${escape(data.text)}</span>
          </div>`;
        },
      },
      onLoad: prepareChoices,
      onChange: prepareChoices,
      controlInput: '<input class="form-control">',
    };

    const options = {
      ...defaultOptions,
      ...this.optionsValue,
    };

    this.select = new TomSelect(this.element, options);
  }

  disconnect() {
    if (this.select) {
      this.select.destroy();
    }
  }
}
