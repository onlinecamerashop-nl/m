class CVBCheckoutButton extends HTMLElement {
    static observedAttributes = ["font-url", "label-url", "label", "info", "click", "onclick"];

    constructor() {
        super();

        // will be set once the component is mounted
        this.generateStyle = function (fontUrl) {
            return "<style></style>"
        }

    }

    static generateStyleCart(fontUrl) {
        return `<style>
    @font-face {
        font-family: 'Graphik';
        src: url('${fontUrl}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    p {
        width: 100%;
        font-family: 'Graphik', sans-serif;
        text-align: center;
        margin-bottom: 6px;
    }

    a {
        width: 100%;
        padding: 16px 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #0000A4;
        border-color: #0000A4;
        border-radius: 8px;
    }

    a img {
        height: 20px;
    }

    a:hover {
        background-color: #0000A499;
        border-color: #0000A499;
        cursor: pointer;
    }

    a.loading img {
        z-index: -1;
    }

    a.loading::after {
        content: "";
        position: relative;
        width: 12px;
        height: 12px;
        left: -100px;
        border: 4px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }

    :host {
        container-type: inline-size;
        contain: layout;
    }

    @container (max-width: 286px) {
        p {
            font-size: 0.8em;
            line-height: 1.4em;
        }

        a {
            padding: 8px 0;
        }

        a img {
            height: 16px;
        }
    }
</style>
`
    }

    static generateStyleCheckout(fontUrl) {
        return `<style>
    @font-face {
        font-family: 'Graphik';
        src: url('${fontUrl}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    :host {
        display: block;
        font-family: 'Graphik', sans-serif;
        /*padding-bottom: 16px;*/
    }

    p {
        width: 100%;
        text-align: center;
        /*padding-bottom: 6px;*/
    }

    a {
        width: 100%;
        padding: 16px 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #0000A4;
        border-color: #0000A4;
        border-radius: 8px;
    }

    a img {
        height: 20px;
    }

    a:hover {
        background-color: #0000A499;
        border-color: #0000A499;
        cursor: pointer;
    }

    a.loading img {
        z-index: -1;
    }

    a.loading::after {
        content: "";
        position: relative;
        width: 12px;
        height: 12px;
        left: -100px;
        border: 4px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }

    @media (min-width: 800px) {
        :host {
            display: flex;
            flex-direction: row-reverse;
        }

        a {
            width: fit-content;
            padding: 6px 24px;
        }

        p {
            text-align: left;
            margin-left: 24px;
        }
    }
</style>
  `
    }

    connectedCallback() {
        switch (this.getAttribute('type')) {
            case 'cart':
                this.generateStyle = CVBCheckoutButton.generateStyleCart;
                break;
            case 'checkout':
                this.generateStyle = CVBCheckoutButton.generateStyleCheckout;
                break;
        }

        const infoText = document.createElement('p')
        infoText.innerText = this.getAttribute('info');


        const button = document.createElement('a')
        const img = document.createElement('img');

        img.src = this.getAttribute('label-url')
        img.alt = this.getAttribute('label')

        button.appendChild(img);

        if (this.onclick != null) {
            button.onclick = CVBCheckoutButton.wrapOnClick(this, this.onclick);
            this.onclick = null;
        }

        const shadow = this.attachShadow({mode: 'open'});

        const style = document.createElement("style");
        style.textContent = this.generateStyle(this.getAttribute('font-url'));

        shadow.appendChild(style);
        shadow.appendChild(infoText);
        shadow.appendChild(button);
    }

    static wrapOnClick(element, fn) {
        return () => {
            const a = element.shadowRoot.querySelector("a");
            a.classList.toggle('loading');
            try {
                Promise.resolve(fn())
                    .then(() => a.classList.toggle('loading'));
            } catch (e) {
                console.error(e)
            }
        }
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (this.shadowRoot == null)
            return;

        switch (name) {
            case 'font-url':
                const style = this.shadowRoot.querySelector("style");
                style.textContent = this.generateStyle(newValue);
                break;
            case 'label-url':
                const img = this.shadowRoot.querySelector("img");
                img.src = newValue;
                break;
            case 'label':
                const imgAlt = this.shadowRoot.querySelector("img");
                imgAlt.alt = newValue;
                break;
            case 'info':
                const txt = this.shadowRoot.querySelector("p");
                txt.innerText = newValue;
                break;
            case 'onclick':
                if (newValue == null)
                    return;
                
                button.onclick = CVBCheckoutButton.wrapOnClick(newValue);
                this.onclick = null;
                break;
            default:
                console.log("unknown attribute change", {name, oldValue, newValue});
                break;
        }
    }
}

customElements.define('cvb-checkout-button', CVBCheckoutButton);
