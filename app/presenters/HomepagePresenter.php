<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

use Nette\Application\UI\Form;
use PdfResponse\PdfResponse;

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends BasePresenter {
	public function createComponentForm($name) {
		$form = new Form($this, $name);
		$form->addTextArea("html", "Upravte data", 80, 50)
			->controlPrototype->class[] = "tinymce";
		$form->addSubmit("doPDFka", "Do PDFka!");

		$appDir = Nette\Environment::getVariable('appDir');
		
		$form->setDefaults(
			array(
				"html" => $this->createTemplate()->setFile($appDir."/templates/Homepage/pdf-source.latte")->__toString()
			)
		);

		$form->onSuccess[] = array($this,"onSubmit");
	}

	public function onSubmit(Form $form) {
		$values = $form->values;

		// Create PDFResponse object
		$pdf = new PDFResponse($values["html"]);

		// Všechny tyto konfigurace jsou volitelné:

		// Orientace stránky
		$pdf->pageOrientation = PDFResponse::ORIENTATION_LANDSCAPE;
		// Formát stránky
		$pdf->pageFormat = "A0";
		// Okraje stránky
		$pdf->pageMargins = "100,100,100,100,20,60";
		// Způsob zobrazení PDF
		$pdf->displayLayout = "continuous";
		// Velikost zobrazení
		$pdf->displayZoom = "fullwidth";
		// Název dokumentu
		$pdf->documentTitle = "Nadpis stránky";
		// Dokument vytvořil:
		$pdf->documentAuthor = "Jan Kuchař";

		// Ignorovat styly v html (v tagu <style>?)
		//$pdf->ignoreStylesInHTMLDocument = true;

		// Další styly mimo HTML dokument
		//$pdf->styles .= "p {font-size: 80%;}";

		// Callback - těsně před odesláním výstupu do prohlížeče
		//$pdfRes->onBeforeComplete[] = "test";

		$pdf->mPDF->IncludeJS("app.alert('This is alert box created by JavaScript in this PDF file!',3);");
		$pdf->mPDF->IncludeJS("app.alert('Now opening print dialog',1);");
		$pdf->mPDF->OpenPrintDialog();

		// Ukončíme presenter -> předáme řízení PDFresponse
		$this->sendResponse($pdf);
	}

}
