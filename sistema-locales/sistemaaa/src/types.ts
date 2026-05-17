/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

export enum StaffRole {
  ADMIN = 'Administrador',
  SELLER = 'Vendedor',
  CM = 'CM'
}

export enum EventType {
  WEDDING = 'Boda',
  CORPORATE = 'Corporativo',
  BIRTHDAY = 'Cumpleaños',
  SOCIAL = 'Social'
}

export enum EventStatus {
  CONFIRMED = 'Confirmado',
  PENDING = 'Pendiente',
  CANCELLED = 'Cancelado',
  CLOSED = 'Cerrado'
}

export interface EventRecord {
  id: string;
  clientName: string;
  clientId: string;        // ID/Document number of the client
  eventType: string;
  date: string;
  guests: number;
  status: EventStatus;
  totalAmount: number;
  advancePayment: number;      // Initial payment
  balancePending: number;      // Amount remaining
  paymentDueDate: string;     // Estimated date for final payment
  signedContractUrl?: string;  // Simulated URL for the uploaded signed contract
  sellerName?: string;         // Name of the seller who registered the event
}

export interface InventoryItem {
  id: string;
  name: string;
  category: 'Mobiliario' | 'Catering' | 'Tecnología' | 'Decoración' | 'Bebidas' | 'Consumibles';
  boxes: number;           // Total closed boxes
  unitsPerBox: number;     // Conversion factor
  looseUnits: number;      // Units not in boxes (either open box or loose stock)
  pricePerBox: number;
  pricePerUnit: number;
  status: 'In Stock' | 'Low Stock' | 'Maintenance';
}

export interface SaleRecord {
  id: string;
  eventId: string;
  clientName: string;
  items: {
    name: string;
    quantity: number;
    type: 'Unidad' | 'Caja';
    subtotal: number;
  }[];
  amount: number;
  cashReceived: number;        // Monto recibido
  changeGiven: number;         // Cambio entregado
  date: string;
  paymentMethod: 'Efectivo' | 'QR' | 'Tarjeta' | 'Transferencia';
  status: 'Paid' | 'Partial' | 'Refunded';
  sellerName?: string;         // Name of the person who processed the sale
  isPrinted?: boolean;         // Tracks if the ticket has been printed
}

export interface StaffMember {
  id: string;
  name: string;
  role: 'Administrador' | 'Vendedor' | 'Mesero' | 'Seguridad' | 'Limpieza' | 'Catering' | 'CM';
  username: string;
  password?: string;
  email: string;
  status: 'Active' | 'On Leave';
  avatar: string;
}
