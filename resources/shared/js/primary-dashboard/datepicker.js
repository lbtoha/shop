import { create, LockPlugin, RangePlugin } from "@easepick/bundle";
import Litepicker from "litepicker";

export const datePicker = (id, singleMode = false) => {
    const element = document.getElementById(id);

    if (!element) {
        return null;
    }

    const picker = new Litepicker({
        element: document.getElementById(id),
        singleMode: singleMode,
        tooltipText: {
            one: "night",
            other: "nights",
        },
        

        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
    });

    return picker;
};

export function datePickerInit(className, singleMode = false, disableDateList = []) {
    const elements = document.querySelectorAll(`.${className}`);
    if (!elements?.length) {
        return null;
    }
    elements.forEach(function (element) {
        // set value from query params

        if (element.easepick) {
            element.easepick.destroy();
        }

        const picker = new create({
            element, // Pass the DOM element directly
            css: ["https://cdn.jsdelivr.net/npm/@easepick/bundle/dist/index.css"],
            plugins: singleMode ? [LockPlugin] : [RangePlugin, LockPlugin],
            RangePlugin: singleMode
                ? undefined
                : {
                      tooltip: true,
                  },
            LockPlugin: {
                minDate: new Date(),
            },
            calendars: singleMode ? 1 : window.innerWidth > 768 ? 2 : 1,
            zIndex: 20,
            disable: disableDateList, // Disable specific dates
            grid: singleMode ? 1 : window.innerWidth > 768 ? 2 : 1,
            setup: (picker) => {
                picker.on("show", () => {
                    setTimeout(() => {
                        const shadow = picker.ui.shadowRoot;
                        if (shadow) {
                            const pickerEl = shadow.querySelector(".container");
                            if (pickerEl) {
                                pickerEl.style.left = "auto";
                                pickerEl.style.right = "0px";
                            }
                        }
                    }, 10);
                });
            },
        });

        element.easepick = picker;
    });
}
