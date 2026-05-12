<?php

namespace App\Controller;

use App\Enum\Status;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        InvoiceRepository $invoiceRepository,
        ClientRepository $clientRepository,
        ProductRepository $productRepository
    ): Response {

 
        $user = $this->getUser();
        $user= ["firstName"=>"zut", "lastName"=>"oshiri", "id"=>1];
    
        // Toutes les factures de l'utilisateur
        $invoices = $invoiceRepository->findBy(
            ['user' => $user],
            ['id' => 'DESC']
        );

        // Tous les clients
        $clients = $clientRepository->findBy([
            'user' => $user
        ]);

        // Tous les produits
        $products = $productRepository->findBy(['user' => $user]);
        // chiffre d'affaire
        $revenue = 0;

        foreach ($invoices as $invoice) {

            // On additionne uniquement les factures payées
            if ($invoice->getStatus() === Status::PAID) {

                $revenue += $invoice->getTotalTtc();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FACTURES EN ATTENTE
        |--------------------------------------------------------------------------
        */

        $pendingInvoices = 0;

        foreach ($invoices as $invoice) {

            if ($invoice->getStatus() === Status::PENDING_PAYMENT) {

                $pendingInvoices++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RENDER
        |--------------------------------------------------------------------------
        */

        return $this->render('dashboard/index.html.twig', [
              
            // Factures récentes
            'invoices' => \array_slice($invoices, 0, 5),

            // Clients
            'clients' => $clients,

            // Produits
            'products' => $products,

            // Stats dashboard
            'revenue' => $revenue,
            'pendingInvoices' => $pendingInvoices,
        ]);
    }
}