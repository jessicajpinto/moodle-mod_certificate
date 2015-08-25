<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A4_non_embedded certificate type
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 20;
    $sealy = 20;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 75;
    $wmarky = 35;
    $wmarkw = 150;
    $wmarkh = 150;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else { // Portrait
    $x = 10;
    $y = 40;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
}

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Adicionando o cabeçalho do certificado
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $sealx + 30, $sealy + 3, 'L', 'Helvetica', 'B', 18, "UNIVERSIDADE FEDERAL DE SERGIPE");
certificate_print_text($pdf, $sealx + 30, $sealy + 13, 'L', 'Helvetica', '', 18, "PRÓ-REITORIA DE GESTÃO DE PESSOAS");
certificate_print_text($pdf, $sealx + 30, $sealy + 23, 'L', 'Helvetica', '', 18, "DEPARTAMENTO DE DESENVOLVIMENTO DE RECURSOS HUMANOS");
certificate_print_text($pdf, $sealx + 30, $sealy + 33, 'L', 'Helvetica', '', 18, "DIVISÃO DE DESENVOLVIMENTO DE PESSOAL");
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, 80, 'C', 'Times', '', 32, "CERTIFICADO");

// Adicionando texto de certificação que informa nome completo, data de início e fim do curso e sua carga horaria
$nome_completo = fullname($USER); //Recuperando nome completo do usuario
$data_inicio = date('d/m/Y', $course->startdate); //Recuperando data de início cadastrado nas configurações do curso 
$cocc = $DB->get_record('course_completion_criteria', array('course'=>$course->id, 'criteriatype'=>COMPLETION_CRITERIA_TYPE_DATE), '*', MUST_EXIST); // Recuperando 
$data_final = date('d/m/Y', $cocc->timeend);
if ($certificate->printhours) {
   $carga_horaria = $certificate->printhours;
}
 else {
     $carga_horaria = 0;
}
$texto_certificacao = "Certificamos que o(a) Sr(a) $nome_completo exerceu atividade de Coordenador Técnico do curso $course->fullname, promovido pela Divisão de Desenvolvimento de Pessoal - DIDEP/DDRH/PROGEP, no período de $data_inicio a $data_final, com carga horária total de $carga_horaria horas.";
certificate_print_text($pdf, $x + 10, $y + 80, 'J', 'Times', '', 15, $texto_certificacao, 258);

// Local e data de impressão do certificado
$data_impressao = strftime('%d de %B de %Y', certificate_get_date_unformated($certificate, $certrecord, $course));
certificate_print_text($pdf, $x, $y + 115, 'R', 'Times', '', 15, "Cidade Universitária Prof. José Aloísio de Campos, $data_impressao.", 268);

// Impressão das assinaturas
certificate_imprimirImagemEspecifica($pdf, "$CFG->dirroot/mod/certificate/pix/signatures/Ass_Solange.png", "$CFG->dataroot/mod/certificate/pix/signatures/Ass_Solange.png", $x+30, $y+132, '', '');
certificate_print_text($pdf, $x+15, $y+140, 'L', 'Times', '', 15, "________________________");
certificate_print_text($pdf, $x+15, $y+145, 'L', 'Times', '', 15, "Solange Melo do Nascimento");
certificate_print_text($pdf, $x+30, $y+150, 'L', 'Times', '', 15, "Chefe da DIDEP");
certificate_imprimirImagemEspecifica($pdf, "$CFG->dirroot/mod/certificate/pix/signatures/Ass_Rose.png", "$CFG->dataroot/mod/certificate/pix/signatures/Ass_Rose.png", $x+80, $y+138, '', '');
certificate_print_text($pdf, $x+88, $y+140, 'L', 'Times', '', 15, "________________________________");
certificate_print_text($pdf, $x+88, $y+145, 'L', 'Times', '', 15, "Rose Maria Tavares Fagundes Ferreira");
certificate_print_text($pdf, $x+110, $y+150, 'L', 'Times', '', 15, "Diretora do DDRH");
certificate_imprimirImagemEspecifica($pdf, "$CFG->dirroot/mod/certificate/pix/signatures/Ass_Ednalva.png", "$CFG->dataroot/mod/certificate/pix/signatures/Ass_Ednalva.png", $x+194, $y+134, '', '');
certificate_print_text($pdf, $x+180, $y+140, 'L', 'Times', '', 15, "_____________________________");
certificate_print_text($pdf, $x+195, $y+145, 'L', 'Times', '', 15, "Ednalva Freire Caetano");
certificate_print_text($pdf, $x+185, $y+150, 'L', 'Times', '', 15, "Pró-Reitora de Gestão de Pessoas");

//Adicionando página do conteúdo programático e do código de autenticação.
$pdf->AddPage();
$pdf->lastPage();

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency e colocando marca d'agua.
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
// Configurando alpha para voltar ao normal
$pdf->SetAlpha(1);

// Adicionando quadro com código de autenticação, dia e horário de autenticação.
$codigoAutenticacao = certificate_get_code($certificate, $certrecord);
$dataImpressaoVerso = date('d/m/Y\, \á\s G\:i', certificate_get_date_unformated($certificate, $certrecord, $course));
certificate_criarRetanguloAutenticacao($pdf, $certificate);
certificate_print_text($pdf, 152, 144, 'C', 'Helvetica', '', 10, "UNIVERSIDADE FEDERAL DE SERGIPE");
certificate_print_text($pdf, 152, 148, 'C', 'Helvetica', '', 10, "PRÓ-REITORIA DE GESTÃO DE PESSOAS");
certificate_print_text($pdf, 152, 152, 'C', 'Helvetica', '', 9, "DEPARTAMENTO DE DESENVOLVIMENTO DE RECURSOS HUMANOS");
certificate_print_text($pdf, 152, 156, 'C', 'Helvetica', '', 9, "DIVISÃO DE DESENVOLVIMENTO DE PESSOAL");
certificate_print_text($pdf, 161, 162, 'J', 'Helvetica', '', 9, "O certificado de $nome_completo foi registrado no ambiente virtual de aprendizagem da Universidade Corporativa da UFS - UcUFS sob o código $codigoAutenticacao em $dataImpressaoVerso horas.", 117);
certificate_imprimirImagemEspecifica($pdf, "$CFG->dirroot/mod/certificate/pix/signatures/Rub_Solange.png", "$CFG->dataroot/mod/certificate/pix/signatures/Rub_Solange.png", 211, 176, '', '');
certificate_print_text($pdf, 152, 185, 'C', 'Helvetica', '', 9, "________________________");
certificate_print_text($pdf, 152, 188, 'C', 'Helvetica', '', 9, "Chefe da DIDEP");

?>