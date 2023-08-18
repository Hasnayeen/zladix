<div
    x-data="{
        prevMotionAttribute: null,
        fn: null,
        get open () {return this.modelValue === this.value},
        get showSlot () {
            return (this.element == undefined) || (this.element == null) ? true : false;
        },
        // @fn init
        init () {
            this.size = {
                width: $el.offsetWidth,
                height: $el.offsetHeight
            }
            node = $el.children[0];
            this.contentElement = node;
            this.contentElement.setAttribute('data-value', this.value);
            if (this.viewport) {
                this.onViewportContentChange(this.value, node);
            }
            this.fn = this.onFocusOutside($el, (ev) => {
                this.handleContentExit();
                const target = ev.target;
                // Only dismiss content when focus moves outside of the menu
                if (this.rootNavigationMenu?.contains(target)) ev.preventDefault();
            });
        },
        // @fn destroy
        destroy () {
            this.fn();
        },
        // @fn motionAttribute
        get motionAttribute () {
            const items = this.getItems();
            const values = items.map((item) => item.id.split('trigger-')[1]);
            if (this.dir === 'rtl') values.reverse();
            const index = values.indexOf(this.modelValue);
            const prevIndex = values.indexOf(this.previousValue);
            const isSelected = this.value === this.modelValue;
            const wasSelected = prevIndex === values.indexOf(this.value);

            // We only want to update selected and the last selected content
            // this avoids animations being interrupted outside of that range
            if (!isSelected && !wasSelected) return this.prevMotionAttribute;

            const attribute = (() => {
                // Don't provide a direction on the initial open
                if (index !== prevIndex) {
                    // If we're moving to this item from another
                    if (isSelected && prevIndex !== -1)
                        return index > prevIndex ? 'from-end' : 'from-start';
                    // If we're leaving this item for another
                    if (wasSelected && index !== -1)
                        return index > prevIndex ? 'to-start' : 'to-end';
                }
                // Otherwise we're entering from closed or leaving the list
                // entirely and should not animate in any direction
                return null;
            })();

            this.prevMotionAttribute = attribute;
            return attribute;
        },
        // @fn handleKeydown
        handleKeydown (ev) {
            const isMetaKey = ev.altKey || ev.ctrlKey || ev.metaKey;
            const isTabKey = ev.key === 'Tab' && !isMetaKey;
            const candidates = this.getTabbableCandidates(ev.currentTarget);

            if (isTabKey) {
                const focusedElement = document.activeElement;
                const index = candidates.findIndex(
                    (candidate) => candidate === focusedElement
                );
                const isMovingBackwards = ev.shiftKey;
                const nextCandidates = isMovingBackwards
                    ? candidates.slice(0, index).reverse()
                    : candidates.slice(index + 1, candidates.length);

                if (this.focusFirst(nextCandidates)) {
                    // prevent browser tab keydown because we've handled focus
                    ev.preventDefault();
                } else {
                    // If we can't focus that means we're at the edges
                    // so focus the proxy and let browser handle
                    // tab/shift+tab keypress on the proxy instead
                    this.focusProxyRef?.focus();
                    return;
                }
            }

            const newSelectedElement = this.useArrowNavigation(
                ev,
                document.activeElement,
                undefined,
                { itemsArray: candidates, loop: false }
            );
            newSelectedElement?.focus();
            ev.preventDefault();
        },
    }"
    x-effect="init"
    ref="elementRef"
    :id="baseId + '-content-' + value"
    :aria-labelledby="baseId + '-trigger-' + value"
    :data-motion="motionAttribute"
    :data-state="open ? 'open' : 'closed'"
    data-content-impl
    :style="{
        pointerEvents: !open && isRootMenu ? 'none' : undefined,
    }"
    @keydown="handleKeydown"
    @keydown.escape.prevent="$dispatch('escape', {ev})"
    {{ $attributes }}
>
    {{ $slot }}
</div>
