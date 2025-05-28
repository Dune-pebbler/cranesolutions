let themeCurrentMLElementQuery = false;

jQuery(document).ready(function () {
    jQuery('.select2-js').select2();

    // Koppel een klikgebeurtenis aan de button
    setThemeAdminListeners();

    jQuery('.is-copy-target').on('click', function (e) {
        let query = jQuery(this);
        let targetString = query.attr('data-target');
        let targetQuery = jQuery(targetString);
        let cloneTargetQuery = targetQuery.clone();
        let amountOfItems = parseInt(jQuery('[name=theme_option_amount_of_partners]').val()) + 1;

        cloneTargetQuery.removeClass('is-copy-holder');
        cloneTargetQuery.removeAttr('id');

        cloneTargetQuery.find('input.theme-image-selector').attr('name', cloneTargetQuery.find('input.theme-image-selector').attr('name').replace("%s", amountOfItems));

        jQuery('[name=theme_option_amount_of_partners]').val(amountOfItems)

        cloneTargetQuery.show();

        query.parents('.form-item').before(cloneTargetQuery);

        setThemeAdminListeners();
    })
});

function setThemeAdminListeners() {
    jQuery('.theme-image-selector').not('.is-loaded').on('click', function (e) {
        e.preventDefault();

        themeCurrentMLElementQuery = jQuery(this);

        openMediaLibrary();

        themeCurrentMLElementQuery.addClass('is-loaded');
    });

    jQuery('.is-remove-image').not('.is-loaded').on('click', function () {
        let containerQuery = jQuery(this).parent().parent();

        if (!confirm("Weet je het zeker?")) return;

        containerQuery.find('img').attr('src', 'https://placehold.it/300x300');
        containerQuery.find('input').val('');

        if (jQuery(this).hasClass("is-delete-partner-item")) {
            let amountOfPartners = parseInt(jQuery('[name=theme_option_amount_of_partners]').val());

            // remove the item.
            containerQuery.parent().remove();
            //  change the amount
            jQuery('[name=theme_option_amount_of_partners]').val(amountOfPartners - 1);
            // we have to alter all the amounts...
            jQuery('[name^=theme_option_partner_logo_]').each(function (i, e) {
                let name = `theme_option_partner_logo_${i}`;

                if (jQuery(this).parents('.is-copy-holder').length > 0) return;

                jQuery(this).attr('name', name);
            })

        }

        jQuery(this).addClass('is-loaded');
    });

    jQuery('.theme-url-selector').not('.is-loaded').on('click', function (e) {
        e.preventDefault();

        let query = jQuery(this);
        let containerQuery = query.parent().parent();

        // Open de WordPress-link dialoog
        wpLink.open();

        // Voeg een gebeurtenis toe voor wanneer een link is toegevoegd of bijgewerkt
        jQuery('#wp-link-submit').off('click').on('click', function (event) {
            event.preventDefault();
            // event.stopImmediatePropagation();
            // Haal de geselecteerde link URL op
            let url = jQuery('#wp-link-url').val();
            let text = jQuery('#wp-link-text').val();
            let title = jQuery('#wp-link-title').val();
            let isBlanked = jQuery('#wp-link-target').prop('checked');
            let options = {
                url: url,
                text: text,
                title: title,
                isBlanked: isBlanked,
            }

            // Vul de link URL in het gewenste input veld
            containerQuery.find('input.url').val(url);
            containerQuery.find('input.text').val(text);
            containerQuery.find('input.title').val(title);
            containerQuery.find('input.is-blanked').val(isBlanked);
            containerQuery.find('.preview-titel').html(options.text)
            containerQuery.find('.preview-url').html(options.url)

            containerQuery.find('.url-options').val(JSON.stringify(options));

            // Sluit de link dialoog
            wpLink.close();
        });

        query.addClass('is-loaded')
    });
}


// Functie voor het openen van de media library
function openMediaLibrary() {
    // Maak een nieuwe media frame aan
    let frame = wp.media({
        title: 'Selecteer afbeelding',
        multiple: false, // Stel in op true als je meerdere afbeeldingen wilt selecteren
        library: {
            type: 'image' // Stel in op 'all' om alle mediabestandstypen weer te geven
        },
        button: {
            text: 'Selecteer'
        }
    });

    // Wanneer een afbeelding is geselecteerd, voer de volgende acties uit
    frame.on('select', function () {
        let containerQuery = themeCurrentMLElementQuery.parent().parent();
        let attachment = frame.state().get('selection').first().toJSON();
        let imageUrl = attachment.url;

        // Vul de geselecteerde afbeelding URL in het input veld
        containerQuery.find('input.theme-image-selector').val(attachment.id);
        containerQuery.find('img').attr('src', imageUrl);
        themeCurrentMLElementQuery = false;
    });

    // Open de media library
    frame.open();
}