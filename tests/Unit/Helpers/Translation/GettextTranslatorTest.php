<?php

namespace Handtuchsystem\Test\Unit\Helpers\Translation;

use Handtuchsystem\Helpers\Translation\GettextTranslator;
use Handtuchsystem\Helpers\Translation\TranslationNotFound;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use Gettext\Translation;
use Gettext\Translations;

class GettextTranslatorTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Helpers\Translation\GettextTranslator::assertHasTranslation()
     */
    public function testNoTranslation()
    {
        $translations = $this->getTranslations();
        $translator = GettextTranslator::createFromTranslations($translations);

        $this->assertEquals('Translation!', $translator->gettext('test.value'));

        $this->expectException(TranslationNotFound::class);
        $this->expectExceptionMessage('//foo.bar');

        $translator->gettext('foo.bar');
    }

    /**
     * @covers \Handtuchsystem\Helpers\Translation\GettextTranslator::translate()
     */
    public function testTranslate()
    {
        $translations = $this->getTranslations();
        $translator = GettextTranslator::createFromTranslations($translations);

        $this->assertEquals('Translation!', $translator->gettext('test.value'));
    }

    /**
     * @covers \Handtuchsystem\Helpers\Translation\GettextTranslator::translatePlural
     */
    public function testTranslatePlural()
    {
        $translations = $this->getTranslations();
        $translator = GettextTranslator::createFromTranslations($translations);

        $this->assertEquals('Translations!', $translator->ngettext('test.value', 'test.value', 2));
    }

    /**
     * @return Translations
     */
    protected function getTranslations(): Translations
    {
        $translation = Translation::create(null, 'test.value')
            ->translate('Translation!')
            ->translatePlural('Translations!');

        $translations = Translations::create();
        $translations->add($translation);

        return $translations;
    }
}
