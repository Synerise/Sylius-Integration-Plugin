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
    const fixedOptions = {};
    const selectInput = this.element;

    Array.from(selectInput.options).forEach((option) => {
      fixedOptions[option.value] = {
        value: option.value,
        text: option.text,
      };
    });

    const defaultOptions = {
      options: fixedOptions,
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
      onChange: (selected) => {
        const values = new Set();
        Array.from(selectInput.options).forEach((option, i, arr) => {
          if (values.has(option.value)) return option.remove();

          values.add(option.value);
          if (selected.includes(option.value)) option.selected = true;
        });
      },
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
