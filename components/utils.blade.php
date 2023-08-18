<template
    x-data="{
        // @fn useArrowNavigation
        useArrowNavigation (
            e,
            currentElement,
            parentElement,
            options = {}
        ) {
            if (!currentElement) return null;

            const {
                arrowKeyOptions = 'both',
                attributeName = 'data-zladix-collection-item',
                itemsArray = [],
                loop = true,
                dir = 'ltr',
                preventScroll = true,
                focus = false,
            } = options;

            const [right, left, up, down, home, end] = [
                e.key === 'ArrowRight',
                e.key === 'ArrowLeft',
                e.key === 'ArrowUp',
                e.key === 'ArrowDown',
                e.key === 'Home',
                e.key === 'End',
            ];
            const goingVertical = up || down;
            const goingHorizontal = right || left;
            if (
                !home &&
                !end &&
                ((!goingVertical && !goingHorizontal) ||
                (arrowKeyOptions === 'vertical' && goingHorizontal) ||
                (arrowKeyOptions === 'horizontal' && goingVertical))
            ) {
                return null;
            }

            const allCollectionItems = parentElement
                ? Array.from(parentElement.querySelectorAll(`[${attributeName}]`))
                : itemsArray;
                console.log('utils', allCollectionItems, parentElement, itemsArray);

            if (!allCollectionItems.length) return null;

            if (preventScroll) {
                e.preventDefault();
            }

            let item = null;

            if (goingHorizontal || goingVertical) {
                const goForward = goingVertical ? down : dir === 'ltr' ? right : left;
                item = this.findNextFocusableElement(allCollectionItems, currentElement, {
                    goForward,
                    loop,
                });
            } else if (home) {
                item = allCollectionItems[0] || null;
            } else if (end) {
                item = allCollectionItems[allCollectionItems.length - 1] || null;
            }

            if (focus) item?.focus();

            return item;
        },

        // @fn findNextFocusableElement
        findNextFocusableElement (
            elements,
            currentElement,
            { goForward, loop },
            iterations = elements.length
        ) {
            if (--iterations === 0) return null;

            const index = elements.indexOf(currentElement);
            const newIndex = goForward ? index + 1 : index - 1;

            if (!loop && (newIndex < 0 || newIndex >= elements.length)) return null;

            const adjustedNewIndex = (newIndex + elements.length) % elements.length;
            const candidate = elements[adjustedNewIndex];
            if (!candidate) return null;

            const isDisabled =
                candidate.hasAttribute('disabled') &&
                candidate.getAttribute('disabled') !== 'false';
            if (isDisabled) {
                return findNextFocusableElement(
                    elements,
                    candidate,
                    { goForward, loop },
                    iterations
                );
            }
            return candidate;
        },

        // @fn onFocusOutside
        onFocusOutside (element, callback) {
            const handleFocusOut = (event) => {
                if (element && !element.contains(event.target)) {
                    callback(event);
                }
            };
            document.addEventListener('click', handleFocusOut, true);
            return () => {
                document.removeEventListener('click', handleFocusOut, true);
            };
        },

        // @fn useStateMachine
        useStateMachine (initialState, machine) {
            const dispatch = (event) => {
                const nextState = machine[initialState][event];
                return nextState ?? initialState;
            }

            return dispatch;
        },

        // @fn resizeObserver
        resizeObserver (target, callback) {
            let observer = null;
            const cleanup = () => {
                if (observer) {
                    observer.disconnect();
                    observer = null;
                }
            };
            this.$watch(
                target,
                (id) => {
                    cleanup();
                    observer = new ResizeObserver(callback);
                    id && observer.observe(document.getElementById(id));
                }
            );

            return cleanup;
        },

    }"
    x-if="true"
>
    {{ $slot }}
</template>
