<template
    x-data="{
        present: !!modelValue,
        init () {
            this.$watch('modelValue', (val) => {
                this.present = !!val;
            })
        },
        // @fn getItems
        getItems (contentElement = null) {
            const ITEM_DATA_ATTR = 'data-zladix-collection-item';
            const collectionNode = contentElement ?? this.collection;

            if (!collectionNode) return [];
            return Array.from(
                collectionNode.querySelectorAll(`[${ITEM_DATA_ATTR}]:not([data-disabled])`)
            );
        },
    }"
    x-if="present"
>
    <x-presence>
        <div
            x-data="{
                size: {
                    width: null,
                    height: null,
                },
                content: null,
                viewportContentList: [],
                cleanupContentObserver: null,
                // @fn activeContent
                get activeContent () { return this.open ? this.modelValue : this.previousValue },
                // @fn open
                get open () { return !!this.modelValue },
                // @fn init
                init () {
                    this.onViewportChange($el);
                    this.$watch('viewportContent', (val) => {
                        if (this.viewportContent) {
                            this.viewportContentList = Array.from(this.viewportContent.values())
                        }
                        const items = document.querySelectorAll('[data-content-impl]');
                        if (items.length > 0) {
                            const activeNode = Array.from(items).find(
                                (i) => i.id === this.baseId + '-content-' + this.activeContent
                            );
                            this.content = activeNode.id;
                        }
                    });
                },
                // @fn handleClose
                handleClose (node) {
                    this.modelValue = '';
                    node.triggerElement.focus();
                    node.closedByEscape = true;
                },
            }"
            ref="primitiveElement"
            :data-state="open ? 'open' : 'closed'"
            :data-orientation="orientation"
            :style="{
                // Prevent interaction when animating out
                pointerEvents: !this.open && this.isRootMenu ? 'none' : undefined,
                // ['--zladix-navigation-menu-viewport-width']: this.viewportSize ? this.viewportSize?.width + 'px' : undefined,
                // ['--zladix-navigation-menu-viewport-height']: this.viewportSize ? this.viewportSize?.height + 'px' : undefined,
            }"
            @pointerenter="onContentEnter(activeContent)"
            @pointerleave="onContentLeave"
            @keydown.escape="handleClose($event.target)"
            {{ $attributes }}
        >
            <template
                x-for="(element, index) in viewportContentList"
                :key="index"
            >
                <template
                    x-data="{
                        value: null,
                        get present () { return this.modelValue === this.value },
                        init () {
                            this.value = this.element.getAttribute('data-value');
                        },
                    }"
                    x-if="true"
                >
                    <x-presence>
                        <div x-html="element.outerHTML"></div>
                    </x-presence>
                </template>
            </template>
        </div>
    </x-presence>
</template>
