<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfService
{
    /**
     * Generate prescription PDF
     */
    public function generatePrescription(Prescription $prescription): string
    {
        // Generate QR code for verification
        $qrCode = base64_encode(QrCode::format('png')->size(150)->generate(
            route('prescriptions.verify', ['number' => $prescription->prescription_number])
        ));

        $prescription->load(['medecin.user', 'patient', 'consultation']);

        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'qrCode' => $qrCode
        ]);

        $filename = 'prescription_' . $prescription->prescription_number . '.pdf';
        $path = storage_path('app/public/prescriptions/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        // Update prescription with PDF path
        $prescription->update([
            'pdf_path' => 'prescriptions/' . $filename,
            'qr_code' => $qrCode
        ]);

        return $path;
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoice(Payment $payment): string
    {
        $payment->load(['patient', 'appointment.medecin']);

        // Generate QR code for invoice verification
        $qrCode = base64_encode(QrCode::format('png')->size(150)->generate(
            route('invoices.verify', ['number' => $payment->invoice_number])
        ));

        $pdf = Pdf::loadView('pdf.invoice', [
            'payment' => $payment,
            'qrCode' => $qrCode
        ]);

        $filename = 'invoice_' . $payment->invoice_number . '.pdf';
        $path = storage_path('app/public/invoices/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        // Update payment with PDF path
        $payment->update([
            'invoice_pdf_path' => 'invoices/' . $filename
        ]);

        return $path;
    }

    /**
     * Generate medical certificate PDF
     */
    public function generateCertificate(array $data): string
    {
        $pdf = Pdf::loadView('pdf.certificate', $data);

        $filename = 'certificate_' . time() . '.pdf';
        $path = storage_path('app/public/certificates/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    /**
     * Generate medical report PDF
     */
    public function generateMedicalReport(array $data): string
    {
        $pdf = Pdf::loadView('pdf.medical-report', $data);

        $filename = 'report_' . time() . '.pdf';
        $path = storage_path('app/public/reports/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }
}
