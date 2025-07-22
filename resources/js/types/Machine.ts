import type { ApiResponse } from './api';
import type { Slot } from './Slot';

export interface Machine {
  machine_id: number;
  name: string;
  location: string;
  status: string;
}

export interface MachineWithSlots extends Machine {
  slots: Slot[];
}

export interface MachineResponse extends ApiResponse<Machine> {}
export interface MachinesResponse extends ApiResponse<Machine[]> {}
export interface MachineWithSlotsResponse extends ApiResponse<MachineWithSlots> {}