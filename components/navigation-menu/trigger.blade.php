@props([
    'disabled' => false,
])

<button
    x-data="{
        disabled: {{ $disabled ? 'true' : 'false' }},
        closedByClick: false,
        openedByPointer: false,
        ref: baseId + '-trigger-' + value,
        get open () {return this.modelValue === this.value},
        // @fn init
        init () {
            this.triggerElement = $el;
            $el.setAttribute('x-ref', this.ref)
        },
        // @fn handlePointerEnter
        handlePointerEnter () {
            this.closedByClick = false;
            this.closedByEscape = false;
        },
        // @fn handlePointerMove
        handlePointerMove (e) {
            if (e.pointerType === 'mouse') {
                if (
                    this.disabled ||
                    this.closedByClick ||
                    this.closedByEscape ||
                    this.openedByPointer
                ) return;
                this.onTriggerEnter(this.value);
                this.openedByPointer = true;
            }
        },
        // @fn handlePointerLeave
        handlePointerLeave (e) {
            if (e.pointerType === 'mouse') {
                if (this.disabled) return;
                this.onTriggerLeave();
                this.openedByPointer = false;
            }
        },
        // @fn handleClick
        handleClick () {
            if (this.open) {
                this.onItemSelect('');
            } else {
                this.onItemSelect(this.value);
            }
            this.closedByClick = this.open;
        },
        // @fn handleKeydown
        handleKeydown (e) {
            const verticalEntryKey = this.dir === 'rtl' ? 'ArrowLeft' : 'ArrowRight';
            const entryKey = { horizontal: 'ArrowDown', vertical: verticalEntryKey }[
                this.orientation
            ];
            if (this.open || e.key === entryKey) {
                this.handleContentEntry();
                e.preventDefault();
                e.stopPropagation();
            }
        },
    }"
    :id="baseId + '-trigger-' + value"
    :disabled="disabled"
    :data-disabled="disabled ? '' : undefined"
    :data-state="open ? 'open' : 'closed'"
    :aria-expanded="open"
    :aria-controls="baseId + '-content-' + value"
    @pointerenter="handlePointerEnter"
    @pointermove="handlePointerMove"
    @pointerleave="handlePointerLeave"
    @click="handleClick"
    @keydown="handleKeydown"
    {{ $attributes }}
    data-zladix-collection-item
>
    {{ $slot }}
</button>

<template x-data="{
        get open () {return this.modelValue === this.value},
    }"
    x-if="open"
>
    <div>
        <x-visually-hidden
            x-data="{
                init () {
                    this.setFocusProxyRef($el);
                },
                setFocusProxyRef (node) {
                    this.focusProxyRef = node;
                    return undefined;
                },
                handleVisuallyHiddenFocus (ev) {
                    const content = this.contentElement?.children[0].parentElement;
                    const prevFocusedElement = ev.relatedTarget;

                    const wasTriggerFocused = prevFocusedElement === this.$refs[this.ref];
                    const wasFocusFromContent = content?.contains(prevFocusedElement);

                    if (wasTriggerFocused || !wasFocusFromContent) {
                        this.handleContentEntry(wasTriggerFocused ? 'start' : 'end');
                    }
                },
            }"
            aria-hidden
            :tabIndex="0"
        ></x-visually-hidden>
        <template x-if="viewport">
            <span :aria-owns="baseId + '-content-' + value"></span>
        </template>
    </div>
</template>
