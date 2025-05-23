<?php

/*
|--------------------------------------------------------------------------
| Validation Language Lines
|--------------------------------------------------------------------------
|
| The following language lines contain the default error messages used by
| the validator class. Some of these rules have multiple versions such
| as the size rules. Feel free to tweak each of these messages here.
|
*/

return [
    'accepted' => ':attribute muss akzeptiert werden.',
    'accepted_if' => ':attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => ':attribute ist keine gültige Internet-Adresse.',
    'after' => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach :date oder gleich :date sein.',
    'alpha' => ':attribute darf nur aus Buchstaben bestehen.',
    'alpha_dash' => ':attribute darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen.',
    'alpha_num' => ':attribute darf nur aus Buchstaben und Zahlen bestehen.',
    'array' => ':attribute muss ein Array sein.',
    'before' => ':attribute muss ein Datum vor :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor :date oder gleich :date sein.',
    'between' => [
        'array' => ':attribute muss zwischen :min & :max Elemente haben.',
        'file' => ':attribute muss zwischen :min & :max Kilobytes groß sein.',
        'numeric' => ':attribute muss zwischen :min & :max liegen.',
        'string' => ':attribute muss zwischen :min & :max Zeichen lang sein.',
    ],
    'boolean' => ':attribute muss entweder \'true\' oder \'false\' sein.',
    'confirmed' => ':attribute stimmt nicht mit der Bestätigung überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':attribute muss ein gültiges Datum sein.',
    'date_equals' => ':attribute muss ein Datum gleich :date sein.',
    'date_format' => ':attribute entspricht nicht dem gültigen Format für :format.',
    'declined' => ':attribute muss abgelehnt werden.',
    'declined_if' => ':attribute muss abgelehnt werden wenn :other :value ist.',
    'different' => ':attribute und :other müssen sich unterscheiden.',
    'digits' => ':attribute muss :digits Stellen haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Stellen haben.',
    'dimensions' => ':attribute hat ungültige Bildabmessungen.',
    'distinct' => ':attribute beinhaltet einen bereits vorhandenen Wert.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => ':attribute muss eine der folgenden Endungen aufweisen: :values',
    'enum' => 'Der ausgewählte Wert ist ungültig.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'extensions' => 'Das Feld :attribute muss eine der folgenden Erweiterungen haben: :values.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => ':attribute muss ausgefüllt sein.',
    'gt' => [
        'array' => ':attribute muss mehr als :value Elemente haben.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'numeric' => ':attribute muss größer als :value sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => ':attribute muss mindestens :value Elemente haben.',
        'file' => ':attribute muss größer oder gleich :value Kilobytes sein.',
        'numeric' => ':attribute muss größer oder gleich :value sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'in_array' => 'Der gewählte Wert für :attribute kommt nicht in :other vor.',
    'integer' => ':attribute muss eine ganze Zahl sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':attribute muss ein gültiger JSON-String sein.',
    'lt' => [
        'array' => ':attribute muss weniger als :value Elemente haben.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => ':attribute muss kürzer als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => ':attribute darf maximal :value Elemente haben.',
        'file' => ':attribute muss kleiner oder gleich :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner oder gleich :value sein.',
        'string' => ':attribute darf maximal :value Zeichen lang sein.',
    ],
    'mac_address' => 'Der Wert muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':attribute darf maximal :max Elemente haben.',
        'file' => ':attribute darf maximal :max Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :max sein.',
        'string' => ':attribute darf maximal :max Zeichen haben.',
    ],
    'mimes' => ':attribute muss den Dateityp :values haben.',
    'mimetypes' => ':attribute muss den Dateityp :values haben.',
    'min' => [
        'array' => ':attribute muss mindestens :min Elemente haben.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'multiple_of' => ':attribute muss ein Vielfaches von :value sein.',
    'not_in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'not_regex' => ':attribute hat ein ungültiges Format.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => 'Das Passwort ist falsch.',
    'present' => ':attribute muss vorhanden sein.',
    'prohibited' => ':attribute ist unzulässig.',
    'prohibited_if' => ':attribute ist unzulässig, wenn :other :value ist.',
    'prohibited_unless' => ':attribute ist unzulässig, wenn :other nicht :values ist.',
    'prohibits' => ':attribute verbietet die Angabe von :other.',
    'regex' => ':attribute hat ein ungültiges Format.',
    'required' => ':attribute muss ausgefüllt werden.',
    'required_array_keys' => 'Dieses Feld muss Einträge enthalten für: :values.',
    'required_if' => ':attribute muss ausgefüllt werden, wenn :other den Wert :value hat.',
    'required_unless' => ':attribute muss ausgefüllt werden, wenn :other nicht den Wert :values hat.',
    'required_with' => ':attribute muss ausgefüllt werden, wenn :values ausgefüllt wurde.',
    'required_with_all' => ':attribute muss ausgefüllt werden, wenn :values ausgefüllt wurde.',
    'required_without' => ':attribute muss ausgefüllt werden, wenn :values nicht ausgefüllt wurde.',
    'required_without_all' => ':attribute muss ausgefüllt werden, wenn keines der Felder :values ausgefüllt wurde.',
    'same' => ':attribute und :other müssen übereinstimmen.',
    'size' => [
        'array' => ':attribute muss genau :size Elemente haben.',
        'file' => ':attribute muss :size Kilobyte groß sein.',
        'numeric' => ':attribute muss gleich :size sein.',
        'string' => ':attribute muss :size Zeichen lang sein.',
    ],
    'starts_with' => ':attribute muss mit einem der folgenden Anfänge aufweisen: :values',
    'string' => ':attribute muss ein String sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => ':attribute ist bereits vergeben.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'url' => ':attribute muss eine URL sein.',
    'uuid' => ':attribute muss ein UUID sein.',

    // Custom rules
    'organization' => ':attribute muss zur Organisation :organization gehören.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'booking_id' => [
            'required' => 'Mindestens eine Anmeldung muss ausgewählt werden.',
        ],
        'house_number' => [
            'regex' => ':attribute muss eine Zahl mit optionalem Buchstabenzusatz sein.',
        ],
        'price' => [
            'prohibited' => 'Ein Preis darf nur gesetzt werden, wenn bei der Organisation eine Bankverbindung hinterlegt ist.',
        ],
        'terms_and_conditions' => [
            'accepted' => 'Die AGB müssen akzeptiert werden. Sonst ist eine Registrierung nicht möglich.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'abilities' => 'Berechtigungen',
        'address' => 'Adresse',
        'approval_status' => 'Freigabestatus',
        'available_from' => 'Beginn des Anmeldezeitraums',
        'available_until' => 'Ende des Anmeldezeitraums',
        'bank_account_holder' => 'Kontoinhaber',
        'bank_name' => 'Name der Bank',
        'booking_id' => 'Anmeldung',
        'booking_option_id' => 'Anmeldeoption',
        'city' => 'Stadt',
        'comment' => 'Kommentar',
        'confirmation_text' => 'Bestätigungstext',
        'country' => 'Land',
        'current_password' => 'Derzeitiges Passwort',
        'date' => 'Datum',
        'date_from' => 'Beginn des Zeitraums',
        'date_of_birth' => 'Geburtsdatum',
        'date_until' => 'Ende des Zeitraums',
        'day' => 'Tag',
        'description' => 'Beschreibung',
        'email' => 'E-Mail-Adresse',
        'event_series_id' => 'Teil der Veranstaltungsreihe',
        'event_series_type' => 'Art der Veranstaltungsreihe',
        'event_type' => 'Art der Veranstaltung',
        'expires_at' => 'Ablaufdatum',
        'file_type' => 'Dateiformat',
        'file' => 'Datei',
        'finished_at' => 'Enddatum',
        'first_name' => 'Vorname',
        'group_id' => 'Gruppe',
        'groups_count' => 'Anzahl der Gruppen',
        'height' => 'Höhe',
        'hour' => 'Stunde',
        'house_number' => 'Hausnummer',
        'iban' => 'IBAN',
        'last_name' => 'Nachname',
        'location_id' => 'Standort',
        'maximum_bookings' => 'Maximale Anmeldungen',
        'method' => 'Methode',
        'minute' => 'Minute',
        'month' => 'Monat',
        'name' => 'Name',
        'organization_id' => 'Organisation',
        'output' => 'Ausgabeformat',
        'paid_at' => 'Bezahlt am',
        'parent_event_id' => 'Teil der Veranstaltung',
        'parent_event_series_id' => 'Teil der Veranstaltungsreihe',
        'parent_organization_id' => 'Übergeordnete Organisation',
        'parent_storage_location_id' => 'Übergeordneter Lagerplatz',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort Bestätigung',
        'payment_due_days' => 'Zahlungsziel',
        'payment_status' => 'Zahlungsstatus',
        'phone' => 'Telefonnummer',
        'position' => 'Position',
        'postal_code' => 'Postleitzahl',
        'price' => 'Preis',
        'publicly_visible' => 'öffentlich sichtbar',
        'register_entry' => 'Registereintrag',
        'representatives' => 'Vertreter',
        'restrictions' => 'Einschränkungen',
        'search' => 'Suchbegriff',
        'second' => 'Sekunde',
        'send_notification' => 'Benutzer per Mail benachrichtigen',
        'size' => 'Größe',
        'slug' => 'Slug',
        'sort' => 'Sortierung',
        'started_at' => 'Startdatum',
        'status' => 'Status',
        'street' => 'Straße',
        'terms_and_conditions' => 'AGB',
        'thickness' => 'Dicke',
        'time' => 'Uhrzeit',
        'title' => 'Titel',
        'trashed' => 'Gelöschte anzeigen?',
        'unit' => 'Einheit',
        'user_role_id' => 'Benutzerrolle',
        'visibility' => 'Sichtbarkeit',
        'website_url' => 'Webauftritt',
        'width' => 'Breite',
        'year' => 'Jahr',
    ],
];
