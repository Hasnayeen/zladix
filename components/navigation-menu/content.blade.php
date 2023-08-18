<template
    x-data="{
        present: modelValue === value,
        init () {
            this.$watch('modelValue', (val) => {
                this.present = val === this.value;
            });
        },
        // @fn handleEscape
        handleEscape (ev) {
            this.closedByEscape = true;
        },
    }"
    x-if="!showOnViewport"
    @escape.window="handleEscape"
>
    <x-presence>
        <x-nav.content-impl>
            {{ $slot }}
        </x-nav.content-impl>
    </x-presence>
</template>
