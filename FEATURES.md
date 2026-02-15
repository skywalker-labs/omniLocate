# OmniLocate Features

## 🌍 Core Geo Intelligence

- **Accurate IP → Location Detection**: Multi-driver architecture (MaxMind, IpApi, Cloudflare, etc.) to pinpoint user location.
- **ISP & ASN Detection**: Identify Internet Service Providers and Autonomous System Numbers.
- **Organization/Owner Info**: Know exactly who owns the network block (e.g., "Google Cloud", "Comcast").
- **Currency & Timezone**: Auto-suggest local currency and timezone based on location.
- **Language Handling**: Auto-detect primary language from country code.

## 🛡️ Network & Proxy Intelligence

- **VPN Detection**: Identify users hiding behind commercial VPN services.
- **Proxy Detection**: Detect anonymous proxies, SOCKS4/5, and HTTP proxies.
- **Hosting/Datacenter**: Flag IPs belonging to hosting providers (AWS, DigitalOcean) often used by bots.
- **Tor Exit Nodes**: Real-time identification of Tor network exit nodes.
- **Geo Persona Classification**: Classify connection type: `Residential`, `Mobile`, `Corporate`, or `Hosting`.

## 🚨 Risk Intelligence Engine

- **Unified Risk Score**: A simplified 0–100 risk score for every IP.
- **Explainable Risk**: Detailed breakdown (e.g., "High Risk: Tor usage + High-Fraud Country").
- **Customizable Weights**: Adjust how much specific factors (like VPN usage) contribute to the final score.
- **Behavioral/Velocity Risk**: Detect attacks based on login frequency from specific Subnets/ASNs.

## ⚙️ Application Integration Layer

- **Smart Middleware**:
  - `GeoRestriction`: Allow/Block specific countries.
  - `GeoRiskGuard`: Block requests exceeding a defined risk threshold.
  - `TorBlocker`: Specific guard against Tor traffic.
- **Dynamic Rule DSL**: Define complex rules like `"country:US;risk<50;is_vpn:false"`.
- **Feature Flags**: Enable/Disable app features based on user region.
- **Adaptive Rate Limiting**: Stricter rate limits for high-risk IPs (Low Risk: 60/min, High Risk: 5/min).

## 🧠 Trust & Accuracy Layer

- **Hybrid Geolocation**: Verify IP location against Browser/Device GPS coordinates to detect spoofing.
- **Verified Bot Intelligence**: Reverse DNS verification to distinguish real Googlebots from fakes.
- **Trusted Header Validation**: Securely parse headers like `CF-IPCountry` to prevent spoofing.
- **Multi-Source Verification**: Option to cross-check IP against multiple drivers for maximum confidence.

## ⚡ Performance & Dashboard

- **Smart Local Cache**: High-performance local caching with TTL to minimize API costs.
- **Background Auto-Refresh**: Keep critical IP data fresh without impacting user latency.
- **Zero-Code Dashboard**: Visual threat map and analytics dashboard to monitor rejected traffic.
- **Analytics Collector**: Log traffic stats by Country, ISP, and Risk Score.

## 🔒 Privacy & Safety

- **Privacy Mode**: Options to hash or anonymize IPs in logs for GDPR/CCPA compliance.

