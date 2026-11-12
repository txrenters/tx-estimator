export type Role = 'superadmin' | 'admin' | 'manager' | 'estimator';
export type StatusValue = 'draft' | 'pending' | 'approved' | 'declined';

export interface CompanySummary {
    id: number;
    name: string;
    short: string;
    color: string;
    tone: string;
    location: string;
    logoUrl: string | null;
    services: string[];
    icon: string;
}

export interface DeskUser {
    id: number;
    name: string;
    email: string;
    role: Role;
    roleLabel: string;
    companyId: number | null;
}

export interface DeskAbilities {
    switchCompany: boolean;
    viewOverview: boolean;
    manageUsers: boolean;
    manageCompany: boolean;
    approve: boolean;
    editCatalog: boolean;
    manageCompanies: boolean;
}

export interface DeskProps {
    user: DeskUser;
    companies: CompanySummary[];
    currentCompany: CompanySummary | null;
    can: DeskAbilities;
    serviceTypes: string[];
    statuses: { value: StatusValue; label: string }[];
}

export interface Line {
    name: string;
    qty: number;
    unit: string;
    labor: number;
    materials: number;
    fixed: number | null;
}

export interface Pricing {
    markup: number;
    discount: number;
    trip: number;
    authorization: number;
    manager: number;
    target: number;
}

export interface BuilderEstimate {
    id: number | null;
    number: string | null;
    title: string;
    status: StatusValue;
    company_id: number | null;
    service_type: string;
    client: string;
    address: string;
    lines: Line[];
    pricing: Pricing;
    scope: string;
    terms: string;
    notes: string;
    creator: string;
    approver: string | null;
    approvedAt: string | null;
    updatedAt: string | null;
    authorization: { name: string; at: string } | null;
}

export interface ShareLink {
    url: string;
    expiresAt: string | null;
    sharedAt: string | null;
    views: number;
    viewedAt: string | null;
}

export interface Revision {
    id: number;
    createdAt: string;
    user: string;
    status: StatusValue;
    total: number;
    snapshot: Partial<BuilderEstimate>;
}


export interface ProposalData {
    number: string;
    title: string;
    client: string | null;
    address: string | null;
    service: string;
    scope: string | null;
    terms: string | null;
    date: string | null;
    company: CompanySummary;
    lines: { name: string; qty: number; unit: string; amount: number }[];
    discount: number;
    trip: number;
    total: number;
}
