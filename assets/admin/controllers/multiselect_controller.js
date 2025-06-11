import {Controller} from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller {
  static values = {
    options: {
      type: Object,
      default: {}
    }
  }

  connect() {
    const defaultOptions = {
      plugins: {
        remove_button: {
          className: 'remove h-4 d-flex align-items-center justify-content-center'
        }
      },
      render: {
        option: function (data, escape) {
          return `<div class="d-flex align-items-center">
                        <span>${escape(data.text)}</span>
                    </div>`;
        },
        item: function (data, escape) {
          return `<div class="d-flex badge bg-blue-lt rounded-2">
                        <span>${escape(data.text)}</span>
                  </div>`;
        }
      },
      controlInput: '<input class="form-control">',
    };

    const options = {
      ...defaultOptions,
      ...this.optionsValue
    };

    this.select = new TomSelect(this.element, options);
  }

  disconnect() {
    if (this.select) {
      this.select.destroy();
    }
  }
}
