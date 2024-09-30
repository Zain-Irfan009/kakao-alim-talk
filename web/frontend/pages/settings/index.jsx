import { useAppBridge } from "@shopify/app-bridge-react";
import { BlockStack, Box, Button, Card, Divider, InlineStack, Layout, Page, Select, Text, TextField } from "@shopify/polaris";
import React, { useCallback, useState } from "react";

export default function Settings() {
  const shopify = useAppBridge();
  // shopify.loading(true);
  const [EditField, setEditField] = useState("");
  const [selectedProfile, setSelectedProfile] = useState("profile_1");

  const handleSelectProfileChange = useCallback((value) => setSelectedProfile(value), []);

  return (
    <Page
      title="Settings"
      primaryAction={{
        content: "Save",
      }}
    >
      <Layout>
        <Layout.AnnotatedSection title="General" description="Manage general settings about the app">
          <BlockStack gap={"400"}>
            <Card>
              <BlockStack gap={"200"}>
                <InlineStack align="space-between" blockAlign="center">
                  <Text as="h2" variant="headingSm">
                    Sender profile
                  </Text>
                  <Button variant="plain" onClick={() => setEditField("Sender profile")}>
                    <Text variant="bodyMd" fontWeight="regular">
                      Edit
                    </Text>
                  </Button>
                </InlineStack>
                {EditField === "Sender profile" ? (
                  <Select
                    label="Profile"
                    labelHidden
                    options={[
                      { label: "Profile 1", value: "profile_1" },
                      { label: "Profile 2", value: "profile_2" },
                      { label: "Profile 3", value: "profile_3" },
                      { label: "Profile 4", value: "profile_4" },
                      { label: "Profile 5", value: "profile_5" },
                    ]}
                    onChange={handleSelectProfileChange}
                    value={selectedProfile}
                  />
                ) : (
                  <Text tone="subdued">Profile 1</Text>
                )}
              </BlockStack>
            </Card>
            {/* <Card padding={"0"}>
              <Box padding={"400"}>
                <BlockStack gap={"200"}>
                  <InlineStack align="space-between" blockAlign="center">
                    <Text as="h2" variant="headingSm">
                      Sender email address
                    </Text>
                    <Button variant="plain">
                      <Text variant="bodyMd" fontWeight="regular">
                        Edit
                      </Text>
                    </Button>
                  </InlineStack>
                  <Text tone="subdued">kakaoalimtalk@sent-with-kakao-alimtalk.com</Text>
                </BlockStack>
              </Box>
              <Box padding={"400"} background="bg-surface-secondary">
                <BlockStack gap={"200"}>
                  <Text>Your email may be shown in inboxes as:</Text>
                  <Text>
                    <Text as="span" fontWeight="semibold">
                      kakaoalimtalk@sent-with-kakao-alimtalk.com
                    </Text>{" "}
                    via{" "}
                    <Text as="span" fontWeight="bold">
                      sent-with-kakao-alimtalk.com
                    </Text>
                  </Text>
                  <Text tone="subdued">
                    <Text as="span" fontWeight="semibold">
                      Recommended:
                    </Text>{" "}
                    Use a custom domain email address to build brand trust and business credibility.
                  </Text>
                </BlockStack>
              </Box>
              <Divider />
            </Card> */}
          </BlockStack>
        </Layout.AnnotatedSection>
        <Layout.Section></Layout.Section>
      </Layout>
    </Page>
  );
}
