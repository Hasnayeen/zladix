@props([
    'ref',
])

<div
    x-data="{
        collection: null,
        init () {
            onIndicatorTrackChange($refs['{{ $ref }}']);
            this.collection = $refs['{{ $ref }}'];
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
    id="{{ $ref }}"
    x-ref="{{ $ref }}"
    style="position: relative;"
>
    <ul {{ $attributes }}>
        {{ $slot }}
    </ul>
</div>
