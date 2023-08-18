@props([
    'value',
])

<li
    x-data="{
        value: '{{ $value }}',
        triggerElement: null,
        contentElement: null,
        closedByEscape: false,
        focusProxyRef: null,
        // @fn restoreContentTabOrder
        restoreContentTabOrder: () => {},
        // @fn handleContentEntry
        async handleContentEntry (side = 'start') {
            const el = this.contentElement?.children[0]?.parentElement;
            if (el) {
                this.restoreContentTabOrder();
                const candidates = this.getTabbableCandidates(el);
                if (candidates.length) {
                    this.focusFirst(side === 'start' ? candidates : candidates.reverse());
                }
            }
        },
        // @fn handleContentExit
        handleContentExit () {
            const el = this.contentElement?.children[0]?.parentElement;
            if (el) {
                const candidates = this.getTabbableCandidates(el);
                if (candidates.length) {
                    this.restoreContentTabOrder = this.removeFromTabOrder(candidates);
                }
            }
        },
        // @fn handleKeydown
        handleKeydown(ev) {
            const currentFocus = document.activeElement;
            if (
                ev.key === 'ArrowUp' ||
                ev.key === 'ArrowDown' ||
                ev.key === 'ArrowLeft' ||
                ev.key === 'ArrowRight'
            ) {
                ev.preventDefault();
            }
            if (ev.key === ' ' || ev.key === 'Enter') {
                if (this.modelValue === this.value) {
                    this.onItemSelect('');
                    this.triggerElement.focus();
                    ev.preventDefault();
                    return;
                } else {
                    ev.target.click();
                    ev.preventDefault();
                    return;
                }
            }
            if (ev.key === 'Escape') {
                this.closedByEscape = true;
                this.triggerElement.focus();
                this.modelValue = '';
                return;
            }
            const newSelectedElement = this.useArrowNavigation(ev, currentFocus, undefined, {
                itemsArray: this.getItems(),
                loop: false,
            });
            newSelectedElement?.focus();
        }
    }"
    @keydown="handleKeydown"
>
    {{ $slot }}
</li>
